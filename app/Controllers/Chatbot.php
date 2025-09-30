<?php

namespace App\Controllers;

use App\Models\FaqModel;
use CodeIgniter\HTTP\ResponseInterface;

class Chatbot extends BaseController
{
    public function index()
    {
        return view('chatbot/index', ['title' => 'LPHS FAQ Chatbot']);
    }

    public function ask(): ResponseInterface
    {
        $this->response->setHeader('Content-Type', 'application/json');
        $text = (string) $this->request->getPost('q');
        $faq = new FaqModel();
        $matches = $faq->searchByKeywords($text, 5);
        // Simple best match
        $answer = null;
        if (!empty($matches)) {
            $answer = $matches[0];
            // increment view count
            $faq->update($answer['id'], ['view_count' => (int)($answer['view_count'] ?? 0) + 1]);
        }
        return $this->response->setJSON([
            'query' => $text,
            'answer' => $answer ? [
                'question' => $answer['question'],
                'answer' => $answer['answer'],
                'category' => $answer['category']
            ] : null
        ]);
    }
} 