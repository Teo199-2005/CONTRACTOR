<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EnrollmentDocumentModel;

class FileController extends BaseController
{
    /**
     * Serve enrollment documents securely
     */
    public function serveEnrollmentDocument($fileName)
    {
        // Check if user is authenticated and has permission
        $auth = auth();
        if (!$auth->loggedIn()) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        // Check if user is admin or the document belongs to them
        $user = $auth->user();
        $documentModel = new EnrollmentDocumentModel();

        // Try to find document by filename in file_path
        $document = $documentModel->like('file_path', $fileName)->first();

        if (!$document) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Document not found']);
        }

        // Allow access if user is admin or if it's their own document
        $hasAccess = $user->inGroup('admin') || $document['student_id'] == $user->id;

        if (!$hasAccess) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Access denied']);
        }

        // Serve the file - check both possible locations
        $filePath1 = WRITEPATH . 'uploads/enrollment_documents/' . $fileName;
        $filePath2 = WRITEPATH . $document['file_path'];

        $filePath = null;
        if (file_exists($filePath1)) {
            $filePath = $filePath1;
        } elseif (file_exists($filePath2)) {
            $filePath = $filePath2;
        }

        if (!$filePath) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'File not found']);
        }

        // Get file info
        $fileInfo = pathinfo($filePath);
        $mimeType = $this->getMimeType($fileInfo['extension']);

        // Set headers
        $this->response->setHeader('Content-Type', $mimeType);
        $this->response->setHeader('Content-Length', filesize($filePath));
        $this->response->setHeader('Content-Disposition', 'inline; filename="' . $document['document_name'] . '"');

        // Output file
        return $this->response->setBody(file_get_contents($filePath));
    }

    /**
     * Serve enrollment documents for admin viewing (less restrictive for images)
     */
    public function serveEnrollmentDocumentForAdmin($fileName)
    {
        // Check if user is authenticated admin
        $auth = auth();
        if (!$auth->loggedIn() || !$auth->user()->inGroup('admin')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        // Find the file in various possible locations
        $possiblePaths = [
            WRITEPATH . 'uploads/enrollment_documents/' . $fileName,
            WRITEPATH . 'uploads/enrollment/' . $fileName,
        ];

        // Also check database for file_path
        $documentModel = new EnrollmentDocumentModel();
        $document = $documentModel->like('file_path', $fileName)->first();
        if ($document) {
            $possiblePaths[] = WRITEPATH . $document['file_path'];
        }

        $filePath = null;
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $filePath = $path;
                break;
            }
        }

        if (!$filePath) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'File not found']);
        }

        // Get file info
        $fileInfo = pathinfo($filePath);
        $mimeType = $this->getMimeType($fileInfo['extension']);

        // Set headers
        $this->response->setHeader('Content-Type', $mimeType);
        $this->response->setHeader('Content-Length', filesize($filePath));
        $this->response->setHeader('Content-Disposition', 'inline; filename="' . $fileName . '"');

        // Output file
        return $this->response->setBody(file_get_contents($filePath));
    }

    /**
     * Get MIME type based on file extension
     */
    private function getMimeType($extension)
    {
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];

        return $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
    }
}
