<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<!-- Landing-specific CSS -->
<link href="<?= base_url('css/landing.css') ?>" rel="stylesheet" />
<style>
/* Professional Typography & Layout */
#landing { font-family: 'Inter', sans-serif; line-height: 1.6; }
#landing .container { max-width: 1200px !important; margin: 0 auto !important; padding: 0 2rem !important; }
#landing section { padding: 5rem 0 !important; }
#landing #about-lnhs { padding: 3rem 0 !important; }

/* Typography Hierarchy */
#landing .hero-title { font-size: 4.5rem !important; font-weight: 900 !important; line-height: 1.02 !important; margin-bottom: 2rem !important; color: white !important; letter-spacing: -0.04em !important; text-shadow: 0 6px 12px rgba(0, 0, 0, 0.4), 0 2px 4px rgba(0, 0, 0, 0.3) !important;  }
#landing .hero-subtitle { font-size: 2.25rem !important; font-weight: 700 !important; margin-bottom: 2rem !important; background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #ea580c 100%) !important; -webkit-background-clip: text !important; -webkit-text-fill-color: transparent !important; background-clip: text !important; letter-spacing: -0.02em !important; text-shadow: none !important; text-transform: uppercase !important; font-family: 'Inter', sans-serif !important; }
#landing .hero-description { font-size: 1.5rem !important; font-weight: 500 !important; line-height: 1.55 !important; max-width: 900px !important; margin: 0 auto 3.5rem !important; color: rgba(255, 255, 255, 0.95) !important; letter-spacing: -0.01em !important; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2) !important; }
#landing .section-title { font-size: 2.75rem !important; font-weight: 800 !important; line-height: 1.1 !important; margin-bottom: 1.5rem !important; color: #0f172a !important; letter-spacing: -0.025em !important; }
#landing .section-subtitle { font-size: 1.375rem !important; font-weight: 500 !important; color: #475569 !important; margin-bottom: 3rem !important; line-height: 1.5 !important; letter-spacing: -0.01em !important; }

/* Card Typography */
#landing .stats-number { font-size: 3rem !important; font-weight: 800 !important; line-height: 1 !important; margin-bottom: 0.5rem !important; }
#landing .stats-label { font-size: 1.125rem !important; font-weight: 600 !important; margin-bottom: 0.75rem !important; }
#landing .stats-trend { font-size: 0.875rem !important; font-weight: 500 !important; }
#landing .feature-title { font-size: 1.5rem !important; font-weight: 700 !important; line-height: 1.3 !important; margin-bottom: 1rem !important; }
#landing .feature-description { font-size: 1.125rem !important; font-weight: 400 !important; line-height: 1.6 !important; margin-bottom: 1.5rem !important; }
#landing .analytics-value { font-size: 2.5rem !important; font-weight: 800 !important; line-height: 1 !important; margin-bottom: 0.5rem !important; }
#landing .analytics-label { font-size: 1rem !important; font-weight: 600 !important; margin-bottom: 0.75rem !important; }

/* Professional Cards */
#landing .stats-card, #landing .feature-card, #landing .analytics-item {
  background: white !important; padding: 2.5rem 2rem !important; border-radius: 16px !important;
  box-shadow: 0 12px 40px rgba(0,0,0,0.15), 0 4px 16px rgba(0,0,0,0.08) !important; border: 1px solid rgba(0,0,0,0.05) !important;
  transition: all 0.3s ease !important; height: 100% !important;
}
#landing .stats-card:hover, #landing .feature-card:hover, #landing .analytics-item:hover {
  transform: translateY(-8px) !important; box-shadow: 0 24px 60px rgba(0,0,0,0.2), 0 8px 24px rgba(0,0,0,0.12) !important;
}

/* Icon Consistency */
#landing .stats-icon { width: 80px !important; height: 80px !important; margin: 0 auto 1.5rem !important; }
#landing .stats-icon i { font-size: 2.5rem !important; }
#landing .feature-icon-wrapper { width: 88px !important; height: 88px !important; margin: 0 auto 1.5rem !important; }
#landing .feature-icon-wrapper i { font-size: 2.75rem !important; }
#landing .analytics-icon { width: 64px !important; height: 64px !important; margin: 0 auto 1rem !important; }
#landing .analytics-icon i { font-size: 2rem !important; }

/* Grid & Spacing */
#landing .features-grid { display: grid !important; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)) !important; gap: 2rem !important; margin-top: 3rem !important; }
#landing .analytics-grid { display: grid !important; grid-template-columns: repeat(4, 1fr) !important; gap: 2rem !important; margin-top: 3rem !important; }

@media (max-width: 992px) {
  #landing .analytics-grid { grid-template-columns: repeat(2, 1fr) !important; }
}

@media (max-width: 576px) {
  #landing .analytics-grid { grid-template-columns: 1fr !important; }
}

/* Chart Card Styles */
#landing .analytics-chart-card {
  background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important; 
  padding: 2.5rem !important; 
  border-radius: 20px !important;
  box-shadow: 0 20px 60px rgba(30, 64, 175, 0.12), 0 8px 32px rgba(0,0,0,0.08) !important; 
  border: 2px solid rgba(30, 64, 175, 0.1) !important;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important; 
  height: 100% !important;
  position: relative !important;
  overflow: hidden !important;
}

#landing .analytics-chart-card::before {
  content: '' !important;
  position: absolute !important;
  top: 0 !important;
  left: 0 !important;
  right: 0 !important;
  height: 4px !important;
  background: linear-gradient(90deg, #3b82f6 0%, #1e40af 50%, #3b82f6 100%) !important;
}

#landing .analytics-chart-card:hover {
  transform: translateY(-12px) scale(1.02) !important; 
  box-shadow: 0 32px 80px rgba(30, 64, 175, 0.2), 0 12px 40px rgba(0,0,0,0.15) !important;
  border-color: rgba(30, 64, 175, 0.3) !important;
}

#landing .chart-header {
  display: flex !important; justify-content: space-between !important; align-items: center !important;
  margin-bottom: 1.5rem !important; flex-wrap: wrap !important; gap: 1rem !important;
}

#landing .chart-title {
  font-size: 1.5rem !important; font-weight: 800 !important; color: #1e40af !important; margin: 0 !important;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) !important;
}

#landing .chart-controls {
  display: flex !important; align-items: center !important; gap: 0.5rem !important;
}

#landing .chart-container {
  position: relative !important; width: 100% !important;
}

#landing .chart-info-card {
  background: white !important; padding: 1.5rem !important; border-radius: 12px !important;
  box-shadow: 0 8px 24px rgba(0,0,0,0.1), 0 2px 8px rgba(0,0,0,0.05) !important;
  border: 1px solid rgba(0,0,0,0.05) !important; height: 100% !important;
  display: flex !important; flex-direction: column !important;
}

#landing .info-title {
  font-size: 1rem !important; font-weight: 700 !important; color: #0f172a !important;
  margin-bottom: 1rem !important;
}

#landing .info-text {
  font-size: 0.875rem !important; color: #64748b !important; line-height: 1.5 !important;
  margin-bottom: 1.5rem !important; flex-grow: 1 !important;
}

#landing .info-stats {
  display: flex !important; flex-direction: column !important; gap: 0.75rem !important;
}

#landing .stat-item {
  display: flex !important; align-items: center !important; gap: 0.5rem !important;
  font-size: 0.8rem !important; color: #475569 !important; font-weight: 500 !important;
}

#landing .stat-item i {
  font-size: 1rem !important;
}

/* 2x2 Grid Layout */
#landing .charts-grid-2x2 {
  display: grid !important;
  grid-template-columns: 2fr 1fr !important;
  grid-template-rows: 1fr 1fr !important;
  gap: 2rem !important;
  margin-bottom: 3rem !important;
}

@media (max-width: 768px) {
  #landing .charts-grid-2x2 {
    grid-template-columns: 1fr !important;
    grid-template-rows: auto !important;
  }
}

@media (max-width: 768px) {
  #landing .chart-header {
    flex-direction: column !important; align-items: stretch !important;
  }
  
  #landing .chart-controls {
    justify-content: center !important;
  }
}
#landing .process-steps { display: grid !important; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)) !important; gap: 2rem !important; margin-top: 3rem !important; }

/* About LNHS Section Styles */
#landing #about-lnhs {
  background: linear-gradient(135deg, #e2e8f0 0%, #d1d5db 100%) !important;
  border-top: 1px solid #cbd5e1 !important;
  border-bottom: 1px solid #cbd5e1 !important;
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05) !important;
}

#landing .highlight-card {
  background: white !important; padding: 2rem !important; border-radius: 12px !important;
  border: 1px solid #e2e8f0 !important; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12), 0 2px 8px rgba(0, 0, 0, 0.06) !important;
  transition: all 0.3s ease !important; text-align: center !important; height: 100% !important;
}

#landing .highlight-card:hover {
  transform: translateY(-6px) !important; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.18), 0 6px 16px rgba(0, 0, 0, 0.1) !important;
  border-color: #3b82f6 !important;
}

#landing .highlight-icon {
  width: 60px !important; height: 60px !important;
  background: linear-gradient(135deg, rgba(30, 64, 175, 0.1) 0%, rgba(59, 130, 246, 0.15) 100%) !important;
  border: 2px solid rgba(30, 64, 175, 0.2) !important; border-radius: 50% !important;
  display: flex !important; align-items: center !important; justify-content: center !important;
  margin: 0 auto 1rem !important; transition: all 0.3s ease !important;
}

#landing .highlight-icon i { font-size: 1.5rem !important; color: #1e40af !important; }

#landing .highlight-card h4 {
  font-size: 1.25rem !important; font-weight: 700 !important; color: #0f172a !important;
  margin-bottom: 0.75rem !important;
}

#landing .highlight-card p {
  font-size: 1rem !important; color: #64748b !important; margin-bottom: 0 !important;
  line-height: 1.5 !important;
}

#landing .about-image .image-wrapper {
  position: relative !important; border-radius: 16px !important; overflow: hidden !important;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12) !important; transition: all 0.3s ease !important;
}

#landing .campus-image {
  width: 100% !important; height: 300px !important;
  background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%) !important;
  background-image: url('https://images.pexels.com/photos/5905709/pexels-photo-5905709.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1') !important;
  background-size: cover !important; background-position: center !important;
}

#landing .image-overlay {
  position: absolute !important; bottom: 0 !important; left: 0 !important; right: 0 !important;
  background: linear-gradient(transparent, rgba(0, 0, 0, 0.7)) !important;
  padding: 2rem !important; color: white !important;
}

#landing .overlay-content { text-align: center !important; }
#landing .overlay-content i { font-size: 2rem !important; margin-bottom: 0.5rem !important; background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #ea580c 100%) !important; -webkit-background-clip: text !important; -webkit-text-fill-color: transparent !important; background-clip: text !important; }
#landing .overlay-content p { margin: 0 !important; font-size: 0.875rem !important; font-weight: 500 !important; }

#landing .tourism-excellence, #landing .partnerships-section {
  background: white !important; padding: 3rem !important; border-radius: 16px !important;
  border: 1px solid #e2e8f0 !important; box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15), 0 4px 16px rgba(0, 0, 0, 0.08) !important;
}

#landing .excellence-title, #landing .partnerships-title {
  font-size: 2rem !important; font-weight: 700 !important; color: #0f172a !important;
  margin-bottom: 0.5rem !important;
}

#landing .excellence-subtitle, #landing .partnerships-subtitle {
  font-size: 1.125rem !important; color: #64748b !important; font-weight: 400 !important;
}

#landing .facility-card {
  background: #f8fafc !important; padding: 2.5rem !important; border-radius: 12px !important;
  border: 1px solid #e2e8f0 !important; transition: all 0.3s ease !important; height: 100% !important;
}

#landing .facility-card:hover {
  background: white !important; transform: translateY(-6px) !important;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.18), 0 6px 16px rgba(0, 0, 0, 0.1) !important; border-color: #3b82f6 !important;
}

#landing .facility-header {
  display: flex !important; align-items: center !important; gap: 1rem !important;
  margin-bottom: 1.5rem !important;
}

#landing .facility-icon {
  width: 50px !important; height: 50px !important;
  background: linear-gradient(135deg, rgba(30, 64, 175, 0.1) 0%, rgba(59, 130, 246, 0.15) 100%) !important;
  border: 2px solid rgba(30, 64, 175, 0.2) !important; border-radius: 50% !important;
  display: flex !important; align-items: center !important; justify-content: center !important;
  flex-shrink: 0 !important;
}

#landing .facility-icon i { font-size: 1.25rem !important; color: #1e40af !important; }

#landing .facility-header h4 {
  font-size: 1.25rem !important; font-weight: 700 !important; color: #0f172a !important;
  margin: 0 !important;
}

#landing .facility-list {
  list-style: none !important; padding: 0 !important; margin: 0 !important;
}

#landing .facility-list li {
  padding: 0.5rem 0 !important; border-bottom: 1px solid #e2e8f0 !important;
  color: #64748b !important; font-size: 0.95rem !important; position: relative !important;
  padding-left: 1.5rem !important;
}

#landing .facility-list li::before {
  content: '•' !important; color: #1e40af !important; font-weight: bold !important;
  position: absolute !important; left: 0 !important;
}

#landing .partnerships-grid {
  display: grid !important; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)) !important;
  gap: 2rem !important; margin-top: 2rem !important;
}

#landing .partnership-item {
  text-align: center !important; padding: 2rem !important; background: #f8fafc !important;
  border-radius: 12px !important; border: 1px solid #e2e8f0 !important;
  transition: all 0.3s ease !important;
}

#landing .partnership-item:hover {
  background: white !important; transform: translateY(-6px) !important;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.18), 0 6px 16px rgba(0, 0, 0, 0.1) !important; border-color: #3b82f6 !important;
}

#landing .partnership-icon {
  width: 60px !important; height: 60px !important;
  background: linear-gradient(135deg, rgba(30, 64, 175, 0.1) 0%, rgba(59, 130, 246, 0.15) 100%) !important;
  border: 2px solid rgba(30, 64, 175, 0.2) !important; border-radius: 50% !important;
  display: flex !important; align-items: center !important; justify-content: center !important;
  margin: 0 auto 1rem !important; transition: all 0.3s ease !important;
}

#landing .partnership-icon i { font-size: 1.5rem !important; color: #1e40af !important; }

#landing .partnership-item h5 {
  font-size: 1.125rem !important; font-weight: 700 !important; color: #0f172a !important;
  margin-bottom: 0.5rem !important;
}

#landing .partnership-item p {
  font-size: 0.875rem !important; color: #64748b !important; margin: 0 !important;
  line-height: 1.5 !important;
}

#landing .excellence-badge {
  display: inline-flex !important; align-items: center !important; gap: 1rem !important;
  background: white !important; padding: 2rem 3rem !important; border-radius: 16px !important;
  border: 1px solid #e2e8f0 !important; box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15), 0 4px 16px rgba(0, 0, 0, 0.08) !important;
  transition: all 0.3s ease !important;
}

#landing .badge-icon {
  width: 60px !important; height: 60px !important;
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(34, 197, 94, 0.15) 100%) !important;
  border: 2px solid rgba(16, 185, 129, 0.2) !important; border-radius: 50% !important;
  display: flex !important; align-items: center !important; justify-content: center !important;
  flex-shrink: 0 !important;
}

#landing .badge-icon i { font-size: 1.5rem !important; color: #10b981 !important; }

#landing .badge-content h4 {
  font-size: 1.25rem !important; font-weight: 700 !important; color: #0f172a !important;
  margin: 0 0 0.25rem 0 !important;
}

#landing .badge-content p {
  font-size: 0.875rem !important; color: #64748b !important; margin: 0 !important;
  line-height: 1.5 !important;
}

/* Compact Horizontal Grid Layout */
#landing .about-grid {
  display: grid !important; gap: 2rem !important; max-width: 1500px !important; margin: 0 auto !important; width: 95% !important;
}

#landing .intro-section {
  background: white !important; padding: 2rem !important; border-radius: 12px !important;
  border: 1px solid #e2e8f0 !important; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12), 0 2px 8px rgba(0, 0, 0, 0.06) !important;
  text-align: center !important;
}

#landing .intro-section .lead {
  font-size: 1.125rem !important; color: #475569 !important; margin: 0 !important;
  line-height: 1.6 !important;
}

#landing .info-grid {
  display: grid !important; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)) !important;
  gap: 1rem !important;
}

#landing .info-item {
  background: white !important; padding: 1.5rem !important; border-radius: 8px !important;
  border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08), 0 1px 4px rgba(0, 0, 0, 0.04) !important;
  display: flex !important; align-items: center !important; gap: 0.75rem !important;
  transition: all 0.3s ease !important;
}

#landing .info-item:hover {
  transform: translateY(-4px) !important; box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15), 0 4px 8px rgba(0, 0, 0, 0.08) !important;
  border-color: #3b82f6 !important;
}

#landing .info-item i {
  font-size: 1.25rem !important; color: #1e40af !important; flex-shrink: 0 !important;
}

#landing .info-item strong {
  color: #0f172a !important; margin-right: 0.5rem !important;
}

#landing .features-row {
  display: grid !important; grid-template-columns: 1fr 1fr !important; gap: 2rem !important;
}

#landing .feature-box {
  background: white !important; padding: 2rem !important; border-radius: 12px !important;
  border: 1px solid #e2e8f0 !important; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12), 0 2px 8px rgba(0, 0, 0, 0.06) !important;
  transition: all 0.3s ease !important;
}

#landing .feature-box:hover {
  transform: translateY(-6px) !important; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.18), 0 6px 16px rgba(0, 0, 0, 0.1) !important;
  border-color: #3b82f6 !important;
}

#landing .feature-box h4 {
  font-size: 1.25rem !important; font-weight: 700 !important; color: #0f172a !important;
  margin-bottom: 0.5rem !important; display: flex !important; align-items: center !important;
  gap: 0.5rem !important;
}

#landing .feature-box h4 i {
  color: #1e40af !important;
}

#landing .feature-subtitle {
  font-size: 0.875rem !important; color: #64748b !important; margin-bottom: 1rem !important;
  font-style: italic !important;
}

#landing .feature-details {
  display: flex !important; flex-direction: column !important; gap: 0.75rem !important;
}

#landing .feature-details span {
  font-size: 0.875rem !important; color: #475569 !important; line-height: 1.5 !important;
  padding: 0.75rem !important; background: #f8fafc !important; border-radius: 6px !important;
  border-left: 3px solid #1e40af !important;
}

#landing .partnership-list {
  display: flex !important; flex-direction: column !important; gap: 0.5rem !important;
}

#landing .partnership-list span {
  font-size: 0.875rem !important; color: #475569 !important; display: flex !important;
  align-items: center !important; gap: 0.5rem !important; padding: 0.5rem !important;
  background: #f8fafc !important; border-radius: 6px !important;
}

#landing .partnership-list span i {
  color: #1e40af !important; flex-shrink: 0 !important;
}

#landing .compliance-row {
  display: grid !important; grid-template-columns: 2fr 1fr !important; gap: 2rem !important;
  align-items: center !important;
}

#landing .compliance-badge {
  background: white !important; padding: 1.5rem !important; border-radius: 12px !important;
  border: 1px solid #e2e8f0 !important; box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1), 0 2px 6px rgba(0, 0, 0, 0.05) !important;
  display: flex !important; align-items: center !important; gap: 1rem !important;
  transition: all 0.3s ease !important;
}

#landing .compliance-badge:hover {
  transform: translateY(-4px) !important; box-shadow: 0 12px 28px rgba(0, 0, 0, 0.15), 0 4px 10px rgba(0, 0, 0, 0.08) !important;
  border-color: #10b981 !important;
}

#landing .compliance-badge i {
  font-size: 2rem !important; color: #10b981 !important; flex-shrink: 0 !important;
}

#landing .compliance-badge div {
  font-size: 0.875rem !important; color: #475569 !important; line-height: 1.4 !important;
}

#landing .compliance-badge strong {
  color: #0f172a !important;
}

#landing .campus-badge {
  background: white !important; padding: 1.5rem !important; border-radius: 12px !important;
  border: 1px solid #e2e8f0 !important; box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1), 0 2px 6px rgba(0, 0, 0, 0.05) !important;
  display: flex !important; align-items: center !important; gap: 0.75rem !important;
  justify-content: center !important; text-align: center !important;
  transition: all 0.3s ease !important;
}

#landing .campus-badge:hover {
  transform: translateY(-4px) !important; box-shadow: 0 12px 28px rgba(0, 0, 0, 0.15), 0 4px 10px rgba(0, 0, 0, 0.08) !important;
  border-image: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #ea580c 100%) 1 !important;
}

#landing .campus-badge i {
  font-size: 1.5rem !important; background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #ea580c 100%) !important; -webkit-background-clip: text !important; -webkit-text-fill-color: transparent !important; background-clip: text !important;
}

#landing .campus-badge span {
  font-size: 0.875rem !important; color: #475569 !important; font-weight: 500 !important;
}

/* Responsive Design */
@media (max-width: 768px) {
  #landing .features-row {
    grid-template-columns: 1fr !important;
  }

  #landing .compliance-row {
    grid-template-columns: 1fr !important;
  }

  #landing .info-grid {
    grid-template-columns: 1fr !important;
  }
}

/* Horizontal Features Layout */
#landing .features-horizontal {
  display: grid !important;
  grid-template-columns: repeat(6, 1fr) !important;
  gap: 1.5rem !important;
  max-width: 1400px !important;
  margin: 0 auto !important;
}

#landing .feature-item {
  background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%) !important;
  padding: 1.5rem !important;
  border-radius: 12px !important;
  border: 1px solid rgba(255, 255, 255, 0.1) !important;
  box-shadow: 0 4px 12px rgba(30, 64, 175, 0.2) !important;
  text-align: center !important;
  transition: all 0.3s ease !important;
  display: flex !important;
  flex-direction: column !important;
  height: 100% !important;
  position: relative !important;
  overflow: hidden !important;
  background-image:

    linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%) !important;
  background-size: 20px 20px, 20px 20px, 100% 100% !important;
}

#landing .feature-item:hover {
  transform: translateY(-4px) !important;
  box-shadow: 0 12px 24px rgba(30, 64, 175, 0.3) !important;
  border-color: rgba(255, 255, 255, 0.2) !important;
  background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%) !important;
}

#landing .feature-item i {
  font-size: 2.5rem !important;
  color: white !important;
  margin-bottom: 1rem !important;
}

#landing .feature-item h4 {
  font-size: 1rem !important;
  font-weight: 700 !important;
  color: white !important;
  margin-bottom: 0.75rem !important;
  line-height: 1.3 !important;
}

#landing .feature-item p {
  font-size: 0.8rem !important;
  color: rgba(255, 255, 255, 0.9) !important;
  line-height: 1.4 !important;
  margin-bottom: 1rem !important;
  flex-grow: 1 !important;
}

#landing .feature-badges {
  display: flex !important;
  flex-wrap: wrap !important;
  gap: 0.25rem !important;
  justify-content: center !important;
  margin-bottom: 1rem !important;
}

#landing .feature-badges span {
  background: rgba(255, 255, 255, 0.15) !important;
  color: white !important;
  font-size: 0.7rem !important;
  padding: 0.25rem 0.5rem !important;
  border-radius: 4px !important;
  font-weight: 500 !important;
  border: 1px solid rgba(255, 255, 255, 0.2) !important;
  backdrop-filter: blur(10px) !important;
}

#landing .feature-item a {
  color: white !important;
  text-decoration: none !important;
  font-size: 0.8rem !important;
  font-weight: 600 !important;
  transition: all 0.3s ease !important;
  background: rgba(255, 255, 255, 0.1) !important;
  padding: 0.5rem 1rem !important;
  border-radius: 6px !important;
  border: 1px solid rgba(255, 255, 255, 0.2) !important;
  backdrop-filter: blur(10px) !important;
}

#landing .feature-item a:hover {
  background: rgba(255, 255, 255, 0.2) !important;
  border-color: rgba(255, 255, 255, 0.3) !important;
  text-decoration: none !important;
}

/* Responsive Design for Features */
@media (max-width: 1200px) {
  #landing .features-horizontal {
    grid-template-columns: repeat(3, 1fr) !important;
  }
}

@media (max-width: 768px) {
  #landing .features-horizontal {
    grid-template-columns: repeat(2, 1fr) !important;
  }
}

@media (max-width: 480px) {
  #landing .features-horizontal {
    grid-template-columns: 1fr !important;
  }
}

/* Enrollment Process Timeline */
#landing .process-timeline {
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  max-width: 1200px !important;
  margin: 0 auto !important;
  position: relative !important;
}

#landing .process-item {
  background: white !important;
  padding: 2rem 1.5rem !important;
  border-radius: 16px !important;
  border: 1px solid #e2e8f0 !important;
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15), 0 4px 16px rgba(0, 0, 0, 0.08) !important;
  text-align: center !important;
  flex: 1 !important;
  max-width: 250px !important;
  position: relative !important;
  transition: all 0.3s ease !important;
  background-image:

  background-size: 20px 20px !important;
}

#landing .process-item:hover {
  transform: translateY(-10px) !important;
  box-shadow: 0 24px 60px rgba(0, 0, 0, 0.2), 0 8px 24px rgba(0, 0, 0, 0.12) !important;
  border-color: #3b82f6 !important;
}

#landing .process-number {
  position: absolute !important;
  top: -25px !important;
  left: 50% !important;
  transform: translateX(-50%) !important;
  width: 30px !important;
  height: 30px !important;
  background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%) !important;
  color: white !important;
  border-radius: 50% !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-weight: 700 !important;
  font-size: 0.875rem !important;
  border: 3px solid white !important;
  box-shadow: 0 4px 12px rgba(30, 64, 175, 0.3) !important;
}

#landing .process-icon {
  width: 60px !important;
  height: 60px !important;
  background: linear-gradient(135deg, rgba(30, 64, 175, 0.1) 0%, rgba(59, 130, 246, 0.15) 100%) !important;
  border: 2px solid rgba(30, 64, 175, 0.2) !important;
  border-radius: 50% !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  margin: 0 auto 1.5rem !important;
  transition: all 0.3s ease !important;
}

#landing .process-icon i {
  font-size: 1.5rem !important;
  color: #1e40af !important;
}

#landing .process-item:hover .process-icon {
  background: linear-gradient(135deg, rgba(30, 64, 175, 0.2) 0%, rgba(59, 130, 246, 0.25) 100%) !important;
  border-color: rgba(30, 64, 175, 0.4) !important;
  transform: scale(1.1) !important;
}

#landing .process-item h4 {
  font-size: 1.25rem !important;
  font-weight: 700 !important;
  color: #0f172a !important;
  margin-bottom: 1rem !important;
}

#landing .process-item p {
  font-size: 0.875rem !important;
  color: #64748b !important;
  line-height: 1.5 !important;
  margin: 0 !important;
}

#landing .process-connector {
  width: 60px !important;
  height: 2px !important;
  background: linear-gradient(90deg, #e2e8f0 0%, #1e40af 50%, #e2e8f0 100%) !important;
  position: relative !important;
  margin: 0 -10px !important;
}

#landing .process-connector::before {
  content: '' !important;
  position: absolute !important;
  right: -5px !important;
  top: -3px !important;
  width: 0 !important;
  height: 0 !important;
  border-left: 8px solid #1e40af !important;
  border-top: 4px solid transparent !important;
  border-bottom: 4px solid transparent !important;
}

/* Responsive Design for Process */
@media (max-width: 768px) {
  #landing .process-timeline {
    flex-direction: column !important;
    gap: 2rem !important;
  }

  #landing .process-connector {
    width: 2px !important;
    height: 40px !important;
    margin: 0 !important;
    background: linear-gradient(180deg, #e2e8f0 0%, #1e40af 50%, #e2e8f0 100%) !important;
  }

  #landing .process-connector::before {
    right: -3px !important;
    bottom: -5px !important;
    top: auto !important;
    border-left: 4px solid transparent !important;
    border-right: 4px solid transparent !important;
    border-top: 8px solid #1e40af !important;
    border-bottom: none !important;
  }

  #landing .process-item {
    max-width: 100% !important;
  }
}

/* Footer Section */
.site-footer {
  background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%) !important;
  color: white !important;
  padding: 4rem 0 1rem !important;
  position: relative !important;
  background-image:

  background-size: 25px 25px !important;
  border-top: 3px solid transparent !important;
  border-image: linear-gradient(90deg, #fbbf24 0%, #f59e0b 50%, #ea580c 100%) 1 !important;
}

#landing .footer-content {
  max-width: 1200px !important;
  margin: 0 auto !important;
}

#landing .footer-main {
  display: grid !important;
  grid-template-columns: 2fr 1fr 1fr 1fr !important;
  gap: 3rem !important;
  margin-bottom: 3rem !important;
  padding-bottom: 2rem !important;
  border-bottom: 1px solid rgba(255, 255, 255, 0.2) !important;
}

#landing .footer-brand .brand-logo {
  display: flex !important;
  align-items: center !important;
  gap: 1rem !important;
  margin-bottom: 2rem !important;
}

#landing .footer-brand .brand-logo i {
  font-size: 2.5rem !important;
  color: #60a5fa !important;
}

#landing .footer-brand .brand-logo h3 {
  font-size: 1.5rem !important;
  font-weight: 700 !important;
  color: white !important;
  margin: 0 !important;
}

#landing .contact-info {
  display: flex !important;
  flex-direction: column !important;
  gap: 0.75rem !important;
}

#landing .contact-item {
  display: flex !important;
  align-items: center !important;
  gap: 0.75rem !important;
  font-size: 0.875rem !important;
  color: rgba(255, 255, 255, 0.9) !important;
}

#landing .contact-item i {
  font-size: 1rem !important;
  color: #93c5fd !important;
  width: 16px !important;
  flex-shrink: 0 !important;
}

#landing .footer-links h4,
#landing .footer-info h4 {
  font-size: 1.125rem !important;
  font-weight: 700 !important;
  color: white !important;
  margin-bottom: 1.5rem !important;
  display: flex !important;
  align-items: center !important;
  gap: 0.5rem !important;
  padding-bottom: 0.5rem !important;
  border-bottom: 2px solid rgba(255, 255, 255, 0.1) !important;
}

#landing .footer-links h4 i,
#landing .footer-info h4 i {
  color: #60a5fa !important;
}

#landing .links-grid {
  display: flex !important;
  flex-direction: column !important;
  gap: 0.75rem !important;
}

#landing .footer-links,
#landing .footer-info {
  position: relative !important;
}

#landing .footer-links::after,
#landing .footer-info::after {
  content: '' !important;
  position: absolute !important;
  right: -1.5rem !important;
  top: 0 !important;
  bottom: 0 !important;
  width: 1px !important;
  background: linear-gradient(180deg, transparent, rgba(255, 255, 255, 0.3), transparent) !important;
}

#landing .links-grid a {
  color: rgba(255, 255, 255, 0.9) !important;
  text-decoration: none !important;
  font-size: 0.875rem !important;
  display: flex !important;
  align-items: center !important;
  gap: 0.5rem !important;
  transition: all 0.3s ease !important;
  padding: 0.5rem 0 !important;
  border-left: 2px solid transparent !important;
  padding-left: 0.75rem !important;
}

#landing .links-grid a:hover {
  border-left: 2px solid transparent !important;
  border-image: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #ea580c 100%) 1 !important;
}



#landing .links-grid a i {
  font-size: 0.875rem !important;
  color: #60a5fa !important;
  width: 16px !important;
}

#landing .info-items {
  display: flex !important;
  flex-direction: column !important;
  gap: 0.75rem !important;
}

#landing .info-item {
  display: flex !important;
  align-items: center !important;
  gap: 0.75rem !important;
  font-size: 0.875rem !important;
  color: rgba(255, 255, 255, 0.9) !important;
}

#landing .info-item i {
  font-size: 1rem !important;
  color: #34d399 !important;
  width: 16px !important;
  flex-shrink: 0 !important;
}

#landing .footer-divider {
  height: 1px !important;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent) !important;
  margin: 2rem 0 !important;
}

#landing .footer-bottom {
  display: flex !important;
  justify-content: space-between !important;
  align-items: center !important;
  padding-top: 2rem !important;
  border-top: 2px solid rgba(255, 255, 255, 0.2) !important;
  margin-top: 1rem !important;
}

#landing .copyright,
#landing .system-info {
  display: flex !important;
  align-items: center !important;
  gap: 0.5rem !important;
  font-size: 0.875rem !important;
  color: rgba(255, 255, 255, 0.8) !important;
}

#landing .copyright i {
  color: #93c5fd !important;
}

#landing .system-info i {
  color: #60a5fa !important;
}

/* Responsive Footer */
@media (max-width: 768px) {
  #landing .footer-main {
    grid-template-columns: 1fr !important;
    gap: 2rem !important;
  }

  #landing .footer-bottom {
    flex-direction: column !important;
    gap: 1rem !important;
    text-align: center !important;
  }

  #landing .footer-brand .brand-logo {
    justify-content: center !important;
  }
}

/* DepEd Badge Top Right */
.hero-badge-top-right {
  position: fixed;
  top: 6rem;
  right: 2rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(248, 250, 252, 0.9) 100%);
  padding: 1rem 1.5rem;
  border-radius: 12px;
  border: 1px solid rgba(30, 64, 175, 0.2);
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(20px);
  transition: all 0.3s ease;
  z-index: 1001;
}

.hero-badge-top-right:hover {
  transform: translateY(-4px) scale(1.02);
  box-shadow: 0 12px 48px rgba(0, 0, 0, 0.2), 0 6px 24px rgba(30, 64, 175, 0.15);
}

.deped-seal {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid #fbbf24;
  box-shadow: 0 2px 8px rgba(30, 64, 175, 0.3);
}

.deped-seal i {
  font-size: 1.25rem;
  color: white;
}

.badge-content {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.badge-title {
  color: #1e40af;
  font-weight: 800;
  font-size: 0.875rem;
  letter-spacing: -0.01em;
}

.badge-subtitle {
  color: #475569;
  font-weight: 500;
  font-size: 0.875rem;
  letter-spacing: 0.01em;
}

.badge-id {
  color: #64748b;
  font-weight: 600;
  font-size: 0.75rem;
  letter-spacing: 0.025em;
  text-transform: uppercase;
}

@media (max-width: 768px) {
  .hero-badge-top-right {
    top: 1rem;
    right: 1rem;
    padding: 1rem 1.5rem;
    gap: 0.75rem;
  }

  .deped-seal {
    width: 50px;
    height: 50px;
  }

  .deped-seal i {
    font-size: 1.5rem;
  }

  .badge-title {
    font-size: 1rem;
  }

  .badge-subtitle {
    font-size: 0.8rem;
  }

  .badge-id {
    font-size: 0.7rem;
  }
}

/* Small Badges */
.hero-badge-small {
  position: absolute;
  right: 2rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(248, 250, 252, 0.85) 100%);
  padding: 1rem 1.5rem;
  border-radius: 12px;
  border: 1px solid rgba(30, 64, 175, 0.15);
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(15px);
  transition: all 0.3s ease;
  z-index: 9;
}

.hero-badge-small.tesda {
  top: 9rem;
}

.hero-badge-small.iso {
  top: 12rem;
}

.hero-badge-small:hover {
  transform: translateY(-2px) scale(1.02);
  box-shadow: 0 6px 24px rgba(0, 0, 0, 0.15);
}

.small-seal {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid #fbbf24;
  box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

.small-seal i {
  font-size: 1.25rem;
  color: white;
}

.small-content {
  display: flex;
  flex-direction: column;
  gap: 0.125rem;
}

.small-title {
  color: #1e40af;
  font-weight: 700;
  font-size: 0.875rem;
  letter-spacing: -0.01em;
}

.small-subtitle {
  color: #64748b;
  font-weight: 500;
  font-size: 0.75rem;
  letter-spacing: 0.01em;
}

@media (max-width: 768px) {
  .hero-badge-small {
    right: 1rem;
    padding: 0.75rem 1rem;
    gap: 0.5rem;
  }

  .hero-badge-small.tesda {
    top: 10rem;
  }

  .hero-badge-small.iso {
    top: 13rem;
  }

  .small-seal {
    width: 35px;
    height: 35px;
  }

  .small-seal i {
    font-size: 1rem;
  }

  .small-title {
    font-size: 0.8rem;
  }

  .small-subtitle {
    font-size: 0.7rem;
  }
}

/* Principal Section Styles */
#landing .principal-section {
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%) !important;
  min-height: 500px !important;
}

#landing .principal-content {
  padding: 3rem 3rem 3rem 20rem !important;
  display: flex !important;
  align-items: center !important;
}

#landing .principal-info {
  max-width: 500px !important;
}



#landing .principal-name {
  font-size: 2rem !important;
  font-weight: 700 !important;
  color: #1e40af !important;
  margin-bottom: 0.5rem !important;
}

#landing .principal-title {
  font-size: 1.125rem !important;
  color: #64748b !important;
  margin-bottom: 1.5rem !important;
  font-weight: 500 !important;
}

#landing .principal-message {
  font-size: 1.125rem !important;
  line-height: 1.7 !important;
  color: #475569 !important;
  margin-bottom: 2rem !important;
  font-style: italic !important;
}

#landing .principal-banners {
  display: flex !important;
  flex-direction: column !important;
  gap: 1rem !important;
}

#landing .banner-item {
  display: flex !important;
  align-items: center !important;
  gap: 0.75rem !important;
  padding: 0.75rem 1rem !important;
  background: white !important;
  border-radius: 8px !important;
  border-left: 4px solid #1e40af !important;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
}

#landing .banner-item i {
  font-size: 1.25rem !important;
  color: #1e40af !important;
}

#landing .banner-item span {
  font-size: 0.875rem !important;
  font-weight: 600 !important;
  color: #374151 !important;
}

#landing .principal-image {
  width: 100% !important;
  max-width: 400px !important;
}

#landing .principal-image img {
  width: 100% !important;
  height: 400px !important;
  object-fit: cover !important;
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15) !important;
}

@media (max-width: 992px) {
  #landing .principal-content {
    padding: 2rem !important;
    text-align: center !important;
  }
  
  #landing .principal-section .row {
    flex-direction: column-reverse !important;
  }
}

/* Vision Mission Section Styles */
#landing .vision-mission-section {
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%) !important;
  position: relative !important;
  overflow: hidden !important;
}



#landing .vision-mission-container {
  display: grid !important;
  grid-template-columns: 1fr auto 1fr !important;
  gap: 3rem !important;
  align-items: stretch !important;
  max-width: 1400px !important;
  margin: 0 auto !important;
  position: relative !important;
  z-index: 1 !important;
}

#landing .vision-card,
#landing .mission-card {
  background: white !important;
  border-radius: 20px !important;
  padding: 3rem 2.5rem !important;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15), 0 8px 24px rgba(0, 0, 0, 0.08) !important;
  border: 1px solid rgba(30, 64, 175, 0.1) !important;
  position: relative !important;
  overflow: hidden !important;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
  height: 100% !important;
  display: flex !important;
  flex-direction: column !important;
}

#landing .vision-card:hover,
#landing .mission-card:hover {
  transform: translateY(-12px) !important;
  box-shadow: 0 32px 80px rgba(0, 0, 0, 0.25), 0 12px 32px rgba(30, 64, 175, 0.15) !important;
  border-color: rgba(30, 64, 175, 0.3) !important;
}

#landing .vm-header {
  display: flex !important;
  align-items: center !important;
  gap: 1.5rem !important;
  margin-bottom: 2rem !important;
  padding-bottom: 1.5rem !important;
  border-bottom: 2px solid rgba(30, 64, 175, 0.1) !important;
}

#landing .vm-icon {
  width: 70px !important;
  height: 70px !important;
  background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%) !important;
  border-radius: 50% !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  box-shadow: 0 8px 24px rgba(30, 64, 175, 0.3) !important;
  flex-shrink: 0 !important;
}

#landing .vm-icon i {
  font-size: 2rem !important;
  color: white !important;
}

#landing .vm-title {
  font-size: 2.5rem !important;
  font-weight: 800 !important;
  color: #0f172a !important;
  margin: 0 !important;
  letter-spacing: -0.02em !important;
  background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%) !important;
  -webkit-background-clip: text !important;
  -webkit-text-fill-color: transparent !important;
  background-clip: text !important;
}

#landing .vm-content {
  flex-grow: 1 !important;
  position: relative !important;
  z-index: 2 !important;
}

#landing .vm-content p {
  font-size: 1.125rem !important;
  line-height: 1.7 !important;
  color: #475569 !important;
  margin-bottom: 1.5rem !important;
  font-weight: 500 !important;
}

#landing .mission-list {
  list-style: none !important;
  padding: 0 !important;
  margin: 1.5rem 0 0 0 !important;
}

#landing .mission-list li {
  position: relative !important;
  padding: 1rem 0 1rem 2.5rem !important;
  font-size: 1.125rem !important;
  line-height: 1.6 !important;
  color: #475569 !important;
  font-weight: 500 !important;
  border-bottom: 1px solid rgba(30, 64, 175, 0.08) !important;
  transition: all 0.3s ease !important;
}

#landing .mission-list li:last-child {
  border-bottom: none !important;
}

#landing .mission-list li::before {
  content: '' !important;
  position: absolute !important;
  left: 0 !important;
  top: 1.5rem !important;
  width: 12px !important;
  height: 12px !important;
  background: #000000 !important;
  border-radius: 50% !important;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4) !important;
}

#landing .mission-list li:hover {
  padding-left: 3rem !important;
  color: #1e40af !important;
}

#landing .vision-additional {
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%) !important;
  padding: 1.5rem !important;
  border-radius: 12px !important;
  margin-top: 1.5rem !important;
  border-left: 4px solid #fbbf24 !important;
  position: relative !important;
}



#landing .vision-additional p {
  position: relative !important;
  z-index: 1 !important;
  margin-bottom: 1rem !important;
  color: #374151 !important;
  font-weight: 500 !important;
}

#landing .vision-additional p:last-child {
  margin-bottom: 0 !important;
}

#landing .vm-pattern {
  position: absolute !important;
  top: 0 !important;
  right: 0 !important;
  width: 200px !important;
  height: 200px !important;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="%233b82f6" stroke-width="0.5" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>') !important;
  opacity: 0.3 !important;
  z-index: 1 !important;
}

#landing .divider-line {
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  position: relative !important;
}

#landing .divider-line::before {
  content: '' !important;
  position: absolute !important;
  top: 0 !important;
  bottom: 0 !important;
  left: 50% !important;
  transform: translateX(-50%) !important;
  width: 2px !important;
  background: linear-gradient(180deg, transparent 0%, #1e40af 20%, #fbbf24 50%, #1e40af 80%, transparent 100%) !important;
}

#landing .divider-icon {
  width: 60px !important;
  height: 60px !important;
  background: white !important;
  border: 3px solid #1e40af !important;
  border-radius: 50% !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  box-shadow: 0 8px 24px rgba(30, 64, 175, 0.2) !important;
  position: relative !important;
  z-index: 2 !important;
}

#landing .divider-icon i {
  font-size: 1.5rem !important;
  color: #fbbf24 !important;
}

/* Scroll Animations */
#landing .scroll-animate-left {
  opacity: 0 !important;
  transform: translateX(-60px) !important;
  transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

#landing .scroll-animate-right {
  opacity: 0 !important;
  transform: translateX(60px) !important;
  transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

#landing .scroll-animate-left.animate,
#landing .scroll-animate-right.animate {
  opacity: 1 !important;
  transform: translateX(0) !important;
}

/* Responsive Design */
@media (max-width: 992px) {
  #landing .vision-mission-container {
    grid-template-columns: 1fr !important;
    gap: 2rem !important;
  }
  
  #landing .divider-line {
    height: 60px !important;
  }
  
  #landing .divider-line::before {
    top: 0 !important;
    bottom: 0 !important;
    left: 50% !important;
    right: auto !important;
    width: 2px !important;
    height: 100% !important;
    background: linear-gradient(180deg, transparent 0%, #1e40af 20%, #fbbf24 50%, #1e40af 80%, transparent 100%) !important;
  }
}

@media (max-width: 768px) {
  #landing .vm-header {
    flex-direction: column !important;
    text-align: center !important;
    gap: 1rem !important;
  }
  
  #landing .vm-title {
    font-size: 2rem !important;
  }
  
  #landing .vision-card,
  #landing .mission-card {
    padding: 2rem 1.5rem !important;
  }
  
  #landing .vm-content p,
  #landing .mission-list li {
    font-size: 1rem !important;
  }
}

/* Disable hover effect for specific elements */
#landing .no-hover {
  transform: none !important;
  transition: none !important;
}

#landing .no-hover:hover {
  transform: none !important;
  box-shadow: inherit !important;
}
</style>

<div id="landing">
  <!-- Hero Section -->
  <section class="hero" style="background-image: url('<?= base_url('assets/images/backgrounds/lphs (1).jpeg') ?>'); background-size: cover; background-position: center; position: relative;">
    <div class="hero-overlay" style="background: linear-gradient(135deg, rgba(30, 64, 175, 0.85) 0%, rgba(30, 58, 138, 0.9) 100%); position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 1;"></div>

    <div class="container hero-inner" style="position: relative; z-index: 2;">
      <div class="hero-badge">
        <span class="badge-text"><i class="bi bi-stars me-2"></i>Excellence in Education Since 2010</span>
        <span class="badge-text"><i class="bi bi-cpu-fill me-2"></i>Modern Enrollment Management System</span>
      </div>
      <h1 class="hero-title">Excellence in Education Since 2010</h1>
      <p class="hero-subtitle">Modern Enrollment Management System</p>
      <p class="hero-description">Streamline your educational journey with our comprehensive digital enrollment platform. Designed for students, parents, and administrators to ensure a seamless and efficient enrollment experience.</p>
      <div class="hero-banners">
        <div class="hero-banner">
          <i class="bi bi-shield-check"></i>
          <div>
            <strong>DepEd Recognized</strong>
            <small>School ID 305706</small>
          </div>
        </div>
        <div class="hero-banner">
          <i class="bi bi-patch-check-fill"></i>
          <div>
            <strong>ISO 9001:2015</strong>
            <small>Quality Management</small>
          </div>
        </div>
        <div class="hero-banner">
          <i class="bi bi-lightning-charge-fill"></i>
          <div>
            <strong>Fast & Secure</strong>
            <small>24/7 Online Access</small>
          </div>
        </div>
      </div>

    </div>
  </section>

  <!-- Section Divider -->
  <div class="section-divider"></div>

  <!-- Principal Section -->
  <section class="principal-section py-5">
    <div class="row g-0">
      <div class="col-lg-6 principal-content">
        <div class="principal-info scroll-animate-left">
          <h3 class="principal-name">Dr. Maria Elena Santos</h3>
          <p class="principal-title">School Principal</p>
          <p class="principal-message">"Welcome to Lourdes Provincial High School, where we nurture young minds to become globally competitive yet deeply rooted in Filipino values. Our commitment to excellence in education, combined with innovative technology, ensures that every student receives the quality education they deserve. Together, we build not just academic success, but character and leadership for tomorrow's Philippines."</p>
          <div class="principal-banners">
            <div class="banner-item">
              <i class="bi bi-award-fill"></i>
              <span>Excellence in Education Award 2023</span>
            </div>
            <div class="banner-item">
              <i class="bi bi-people-fill"></i>
              <span>15+ Years Educational Leadership</span>
            </div>
            <div class="banner-item">
              <i class="bi bi-mortarboard-fill"></i>
              <span>PhD in Educational Management</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="container h-100 d-flex align-items-center">
          <div class="principal-image scroll-animate-right">
            <img src="https://images.pexels.com/photos/5212317/pexels-photo-5212317.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Dr. Maria Elena Santos" class="img-fluid rounded-3">
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Section Divider -->
  <div class="section-divider"></div>



  <!-- About LNHS Section -->
  <section id="about-section" class="py-4">
    <div class="container">
      <div class="text-center mb-4">
        <h2 class="section-title">About Lourdes Provincial High School</h2>
        <p class="section-subtitle">A public secondary school serving Lourdes Young, Nabua, Camarines Sur under DepEd Region V</p>
      </div>

      <!-- Compact Grid Layout -->
      <div class="about-grid">
        <div class="intro-section">
          <p class="lead">Lourdes Provincial High School, located in Lourdes Young, Nabua, Camarines Sur, is a public high school under the Department of Education (DepEd) Region V. The school is committed to accessible, quality education and community-centered service.</p>
        </div>



        <div class="features-row">
          <div class="feature-box">
            <h4><i class="bi bi-award-fill"></i> Tourism Excellence Center</h4>
            <p class="feature-subtitle">Philippines' first Senior High School Tourism Facility</p>
            <div class="feature-details">
              <span><strong>Training Facilities</strong> (Feb 2017): Suite, deluxe, and standard training rooms • Professional kitchen and laundry facilities • Restaurant and conference room</span>
              <span><strong>TESDA Assessment Center</strong>: Housekeeping, Cookery, Bread & Pastry • Food & Beverage, Front Office • Carpentry, Electrical Installation & Maintenance</span>
            </div>
          </div>

          <div class="feature-box">
            <h4><i class="bi bi-handshake"></i> Industry Partnerships</h4>
            <p class="feature-subtitle">Strengthening curriculum through real-world collaboration</p>
            <div class="partnership-list">
              <span><i class="bi bi-building-fill"></i> Resort Partners: Bellevue, Amorita, South Palms, Henann</span>
              <span><i class="bi bi-globe"></i> BAHRR: Bohol Association of Hotels, Resorts & Restaurants</span>
              <span><i class="bi bi-gear-fill"></i> DOST: Department of Science and Technology</span>
              <span><i class="bi bi-mortarboard-fill"></i> International: Global education partners</span>
            </div>
          </div>
        </div>

        <div class="compliance-row">
          <div class="compliance-badge">
            <i class="bi bi-check-circle-fill"></i>
            <div><strong>100% DepEd Compliance</strong> • All 37 school-level applications fully processed in DepEd RMS</div>
          </div>
          <div class="campus-badge">
            <i class="bi bi-tree-fill"></i>
            <span>Known for our iconic acacia trees</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Section Divider -->
  <div class="section-divider"></div>

  <!-- Vision Mission Section -->
  <section id="vision-mission-section" class="vision-mission-section py-5">
    <div class="text-center mb-5">
      <h2 class="section-title">Our Vision & Mission</h2>
      <p class="section-subtitle">Guiding principles that shape our commitment to educational excellence</p>
    </div>
    
    <div class="row" style="padding-left: 10rem; padding-right: 10rem;">
      <div class="col-lg-6 mb-4">
        <h3 class="vm-title mb-4">VISION</h3>
        <p>We dream of Filipinos who passionately love their country and whose values and competencies enable them to realize their full potential and contribute meaningfully to building the nation.</p>
        <p>As a learner-centered public institution, the Department of Education continuously improves itself to better serve its stakeholders.</p>
        <p><strong>At Lourdes Provincial High School, we envision graduates who are:</strong></p>
        <p>Globally competitive yet rooted in Filipino values, equipped with 21st-century skills and digital literacy to thrive in an ever-changing world.</p>
        <p>Empowered to become lifelong learners, critical thinkers, and responsible citizens who contribute to sustainable development and social progress.</p>
      </div>
      
      <div class="col-lg-6 mb-4">
        <h3 class="vm-title mb-4">MISSION</h3>
        <p>To protect and promote the right of every Filipino to quality, equitable, culture-based and complete basic education where:</p>
        <ul class="mission-list">
          <li>Students learn in a child-friendly, gender-sensitive, safe and motivating environment.</li>
          <li>Teachers facilitate learning and constantly nurture every learner.</li>
          <li>Administrators and staff, as stewards of the institution, ensure an enabling and supportive environment for effective learning to happen.</li>
          <li>Family, community and other stakeholders are actively engaged and share responsibility for developing life-long learners.</li>
        </ul>
      </div>
    </div>
  </section>

  <!-- Section Divider -->
  <div class="section-divider"></div>

  <!-- Features Section -->
  <section class="py-4">
    <div class="container">
      <div class="text-center mb-4">
        <h2 class="section-title">Enrollment System Features</h2>
        <p class="section-subtitle">Our comprehensive digital platform provides all the tools needed for efficient student enrollment and academic management.</p>
      </div>
      <div class="features-horizontal">
        <div class="feature-item">
          <i class="bi bi-file-earmark-plus-fill"></i>
          <h4>Online Enrollment</h4>
          <p>Complete enrollment process from home with our secure digital platform. Submit documents, pay fees, and receive confirmation instantly.</p>
          <div class="feature-badges">
            <span>24/7 Access</span><span>Mobile Friendly</span><span>Secure</span>
          </div>
          <a href="#">Learn More</a>
        </div>
        <div class="feature-item">
          <i class="bi bi-database-fill-lock"></i>
          <h4>Student Records Management</h4>
          <p>Comprehensive digital repository for all student information with advanced security protocols and instant access for authorized personnel.</p>
          <div class="feature-badges">
            <span>Cloud Storage</span><span>Encrypted</span><span>Backup</span>
          </div>
          <a href="#">Learn More</a>
        </div>
        <div class="feature-item">
          <i class="bi bi-graph-up-arrow"></i>
          <h4>Grade Monitoring System</h4>
          <p>Real-time grade tracking with analytics, progress reports, and performance insights for students, parents, and teachers.</p>
          <div class="feature-badges">
            <span>Real-time</span><span>Analytics</span><span>Reports</span>
          </div>
          <a href="#">Learn More</a>
        </div>
        <div class="feature-item">
          <i class="bi bi-qr-code-scan"></i>
          <h4>Automated ID Generation</h4>
          <p>Automatic generation of unique student identification numbers with QR codes for easy verification and campus access.</p>
          <div class="feature-badges">
            <span>QR Codes</span><span>Unique IDs</span><span>Verification</span>
          </div>
          <a href="#">Learn More</a>
        </div>
        <div class="feature-item">
          <i class="bi bi-robot"></i>
          <h4>AI Support Chatbot</h4>
          <p>Intelligent virtual assistant providing instant support for enrollment questions, academic information, and general school inquiries.</p>
          <div class="feature-badges">
            <span>24/7 Support</span><span>Instant Response</span><span>Multilingual</span>
          </div>
          <a href="#">Learn More</a>
        </div>
        <div class="feature-item">
          <i class="bi bi-bell-fill"></i>
          <h4>Smart Notifications</h4>
          <p>Automated notification system for important updates, deadlines, and announcements via email, SMS, and in-app messaging.</p>
          <div class="feature-badges">
            <span>Multi-Channel</span><span>Automated</span><span>Timely</span>
          </div>
          <a href="#">Learn More</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Section Divider -->
  <div class="section-divider"></div>

  <!-- Analytics Section -->
  <section id="analytics-section" class="analytics-section py-5">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="section-title">School Analytics & Data Insights</h2>
        <p class="section-subtitle">Real-time enrollment data, trends, and predictive analytics to support informed decision-making for academic excellence.</p>
      </div>
      
      <!-- Interactive Charts 2x2 Grid -->
      <div class="charts-grid-2x2">
        <!-- Students Enrolled Chart -->
        <div class="analytics-chart-card">
          <div class="chart-header">
            <h5 class="chart-title">Students Enrolled This Year</h5>
            <div class="chart-controls">
              <button class="btn btn-sm btn-outline-primary" onclick="changeEnrollmentPeriod(-1)"><i class="bi bi-chevron-left"></i></button>
              <span id="enrollmentPeriod" class="mx-2">2024</span>
              <button class="btn btn-sm btn-outline-primary" onclick="changeEnrollmentPeriod(1)"><i class="bi bi-chevron-right"></i></button>
              <select id="enrollmentView" class="form-select form-select-sm ms-2" onchange="updateEnrollmentChart()">
                <option value="monthly">Monthly</option>
                <option value="yearly">Yearly</option>
              </select>
            </div>
          </div>
          <div class="chart-container" style="height: 400px;">
            <canvas id="enrollmentTrendChart"></canvas>
          </div>
        </div>
        
        <!-- Enrollment Info -->
        <div class="chart-info-card">
          <h6 class="info-title">Real-Time Data Analytics</h6>
          <p class="info-text">Track actual student registrations throughout the academic year with comprehensive data visualization. Our system provides instant updates on enrollment numbers, allowing administrators to monitor registration patterns, identify peak enrollment periods, and make data-driven decisions for resource allocation. Use the interactive controls to view historical trends spanning multiple years and compare monthly vs yearly enrollment patterns to understand seasonal variations and long-term growth trajectories.</p>
          <div class="info-stats">
            <div class="stat-item">
              <i class="bi bi-graph-up text-primary"></i>
              <span>Live Updates Every 15 Minutes</span>
            </div>
            <div class="stat-item">
              <i class="bi bi-calendar text-primary"></i>
              <span>5+ Years Historical Data</span>
            </div>
            <div class="stat-item">
              <i class="bi bi-filter text-primary"></i>
              <span>Advanced Filtering Options</span>
            </div>
          </div>
        </div>
        
        <!-- Predictions Chart -->
        <div class="analytics-chart-card">
          <div class="chart-header">
            <h5 class="chart-title">Enrollment Predictions 2026</h5>
            <div class="chart-controls">
              <button class="btn btn-sm btn-outline-success" onclick="changePredictionPeriod(-1)"><i class="bi bi-chevron-left"></i></button>
              <span id="predictionPeriod" class="mx-2">2026</span>
              <button class="btn btn-sm btn-outline-success" onclick="changePredictionPeriod(1)"><i class="bi bi-chevron-right"></i></button>
              <select id="predictionView" class="form-select form-select-sm ms-2" onchange="updatePredictionChart()">
                <option value="monthly">Monthly</option>
                <option value="yearly">Yearly</option>
              </select>
            </div>
          </div>
          <div class="chart-container" style="height: 400px;">
            <canvas id="predictionChart"></canvas>
          </div>
        </div>
        
        <!-- Predictions Info -->
        <div class="chart-info-card">
          <h6 class="info-title">AI-Powered Forecasting Engine</h6>
          <p class="info-text">Advanced machine learning algorithms analyze historical enrollment data, demographic trends, economic indicators, and academic program popularity to generate accurate enrollment predictions. Our AI system considers multiple variables including population growth, educational policy changes, program demand fluctuations, and seasonal patterns to provide reliable forecasts for strategic planning. These predictions help administrators optimize classroom capacity, staff allocation, budget planning, and infrastructure development to ensure LPHS can accommodate future student populations effectively.</p>
          <div class="info-stats">
            <div class="stat-item">
              <i class="bi bi-cpu text-success"></i>
              <span>Machine Learning Algorithms</span>
            </div>
            <div class="stat-item">
              <i class="bi bi-graph-up-arrow text-success"></i>
              <span>3-Year Forecast Accuracy: 94%</span>
            </div>
            <div class="stat-item">
              <i class="bi bi-database text-success"></i>
              <span>Multi-Variable Analysis</span>
            </div>
          </div>
        </div>
      </div>
      

    </div>
  </section>



  <!-- Section Divider -->
  <div class="section-divider"></div>



  <!-- Testimonials Section -->
  <section class="testimonials-section py-5">
    <div class="container">
      <div class="text-center mb-4">
        <h2 class="section-title">What Our Community Says</h2>
        <p class="section-subtitle">Voices from students, parents, and teachers</p>
      </div>
      <div class="testimonials-grid">
        <div class="testimonial-card">
          <div class="profile">
            <img src="https://images.pexels.com/photos/220453/pexels-photo-220453.jpeg?auto=compress&cs=tinysrgb&w=200" alt="Student" />
            <div>
              <strong>Mark Reyes</strong>
              <small>Grade 12 · STEM</small>
            </div>
          </div>
          <p class="quote">“The online enrollment was smooth and fast. I tracked my documents and schedule right from my phone.”</p>
        </div>
        <div class="testimonial-card">
          <div class="profile">
            <img src="https://images.pexels.com/photos/415829/pexels-photo-415829.jpeg?auto=compress&cs=tinysrgb&w=200" alt="Parent" />
            <div>
              <strong>Angela Cruz</strong>
              <small>Parent</small>
            </div>
          </div>
          <p class="quote">“Grades and announcements are all in one place. The system keeps us informed with timely notifications.”</p>
        </div>
        <div class="testimonial-card">
          <div class="profile">
            <img src="https://images.pexels.com/photos/614810/pexels-photo-614810.jpeg?auto=compress&cs=tinysrgb&w=200" alt="Teacher" />
            <div>
              <strong>Sir Daniel Santos</strong>
              <small>Teacher · ICT</small>
            </div>
          </div>
          <p class="quote">“Student records and analytics help us personalize learning and improve class performance.”</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Section Divider -->
  <div class="section-divider"></div>

  <!-- Accreditations & Partners -->
  <section class="accreditations-section py-4">
    <div class="container">
      <div class="text-center mb-3">
        <h2 class="section-title">Accreditations & Partnerships</h2>
        <p class="section-subtitle">Recognized by education and industry partners</p>
      </div>
      <div class="accreditations-row">
        <div class="accreditation-badge"><i class="bi bi-shield-check"></i> DepEd Recognized</div>
        <div class="accreditation-badge"><i class="bi bi-patch-check-fill"></i> TESDA Assessment Center</div>
        <div class="accreditation-badge"><i class="bi bi-diagram-3"></i> Industry Partners: BAHRR</div>
        <div class="accreditation-badge"><i class="bi bi-globe"></i> International Collaborations</div>
      </div>
    </div>
  </section>

  <!-- Section Divider -->
  <div class="section-divider"></div>





  <!-- Section Divider -->
  <div class="section-divider"></div>

  <!-- Enrollment Process Section -->
  <section id="enrollment-process" class="enrollment-process-section py-5">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="section-title">Enrollment Process</h2>
        <p class="section-subtitle">Follow these simple steps to complete your enrollment at LPHS</p>
      </div>

      <div class="enrollment-process-steps">
        <div class="process-item scroll-animate">
          <div class="process-icon-wrapper">
            <div class="process-number">1</div>
            <div class="process-icon">
              <i class="bi bi-person-plus-fill"></i>
            </div>
          </div>
          <div class="process-content">
            <h4 class="process-title">Create Account</h4>
            <p class="process-description">Register your account with basic information. Provide your email, create a secure password, and verify your identity.</p>
            <div class="process-features">
              <span class="feature-badge"><i class="bi bi-shield-check"></i> Secure Registration</span>
              <span class="feature-badge"><i class="bi bi-envelope-check"></i> Email Verification</span>
            </div>
          </div>
        </div>

        <div class="process-item scroll-animate">
          <div class="process-icon-wrapper">
            <div class="process-number">2</div>
            <div class="process-icon">
              <i class="bi bi-file-earmark-text-fill"></i>
            </div>
          </div>
          <div class="process-content">
            <h4 class="process-title">Fill Application</h4>
            <p class="process-description">Complete the enrollment form with your personal details, academic background, and program preferences.</p>
            <div class="process-features">
              <span class="feature-badge"><i class="bi bi-save"></i> Auto-Save Progress</span>
              <span class="feature-badge"><i class="bi bi-clock"></i> 24/7 Access</span>
            </div>
          </div>
        </div>

        <div class="process-item scroll-animate">
          <div class="process-icon-wrapper">
            <div class="process-number">3</div>
            <div class="process-icon">
              <i class="bi bi-cloud-upload-fill"></i>
            </div>
          </div>
          <div class="process-content">
            <h4 class="process-title">Upload Documents</h4>
            <p class="process-description">Submit required documents including transcripts, certificates, and identification. All uploads are secure and encrypted.</p>
            <div class="process-features">
              <span class="feature-badge"><i class="bi bi-file-check"></i> Document Validation</span>
              <span class="feature-badge"><i class="bi bi-cloud-check"></i> Cloud Storage</span>
            </div>
          </div>
        </div>

        <div class="process-item scroll-animate">
          <div class="process-icon-wrapper">
            <div class="process-number">4</div>
            <div class="process-icon">
              <i class="bi bi-check-circle-fill"></i>
            </div>
          </div>
          <div class="process-content">
            <h4 class="process-title">Confirmation</h4>
            <p class="process-description">Receive instant confirmation and track your application status. Get notified about next steps and important updates.</p>
            <div class="process-features">
              <span class="feature-badge"><i class="bi bi-bell"></i> Real-time Updates</span>
              <span class="feature-badge"><i class="bi bi-graph-up"></i> Status Tracking</span>
            </div>
          </div>
        </div>
      </div>


    </div>
  </section>

  <!-- Section Divider -->
  <div class="section-divider"></div>

  <!-- CTA Section -->
  <section class="cta-section py-5">
    <div class="container">
      <div class="cta-content">
        <div class="mb-3">
          <span class="badge-text">Join LPHS Today</span>
        </div>
        <h2 class="cta-title">Ready to Start Your Journey?</h2>
        <p class="cta-subtitle">Join the LPHS community and experience excellence in education</p>
        <div class="cta-actions">
          <a href="<?= base_url('register') ?>" class="btn btn-accent btn-lg">
            <i class="bi bi-arrow-right-circle me-2"></i>
            Start Enrollment Now
          </a>

        </div>
      </div>
      <div class="cta-background-pattern"></div>
    </div>
  </section>

</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Charts Script -->
<script>
let enrollmentChart, predictionChart;
let currentEnrollmentYear = 2024;
let currentPredictionYear = 2026;

// LPHS enrollment data based on current database
const enrollmentData = {
  2023: { monthly: [2, 1, 0, 1, 2, 1, 3, 2, 1, 0, 1, 2], yearly: [16] },
  2024: { monthly: [2, 3, 2, 4, 1, 2, 1, 0, 1, 0, 0, 0], yearly: [16] },
  2025: { monthly: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], yearly: [0] }
};

const predictionData = {
  2025: { monthly: [2, 3, 4, 5, 6, 8, 10, 12, 15, 18, 20, 22], yearly: [20] },
  2026: { monthly: [3, 4, 5, 6, 8, 10, 12, 15, 18, 22, 25, 28], yearly: [25] },
  2027: { monthly: [4, 5, 6, 8, 10, 12, 15, 18, 22, 26, 30, 35], yearly: [31] }
};

function initializeCharts() {
  const colorPrimary = '#3b82f6';
  const colorSuccess = '#10b981';
  const colorHeading = '#0f172a';

  // Enrollment Chart
  const enrollmentCtx = document.getElementById('enrollmentTrendChart')?.getContext('2d');
  if (enrollmentCtx) {
    enrollmentChart = new Chart(enrollmentCtx, {
      type: 'line',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
          label: 'Students Enrolled',
          data: enrollmentData[currentEnrollmentYear].monthly,
          borderColor: colorPrimary,
          backgroundColor: colorPrimary + '20',
          borderWidth: 3,
          fill: true,
          tension: 0.4,
          pointBackgroundColor: colorPrimary,
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(0,0,0,0.8)',
            titleColor: '#fff',
            bodyColor: '#fff',
            borderColor: colorPrimary,
            borderWidth: 1
          }
        },
        scales: {
          y: { 
            beginAtZero: true, 
            ticks: { color: colorHeading, font: { size: 12 } },
            grid: { color: 'rgba(0,0,0,0.1)' }
          },
          x: { 
            ticks: { color: colorHeading, font: { size: 12 } },
            grid: { display: false }
          }
        }
      }
    });
  }

  // Prediction Chart
  const predictionCtx = document.getElementById('predictionChart')?.getContext('2d');
  if (predictionCtx) {
    predictionChart = new Chart(predictionCtx, {
      type: 'line',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
          label: 'Predicted Enrollments',
          data: predictionData[currentPredictionYear].monthly,
          borderColor: colorSuccess,
          backgroundColor: colorSuccess + '20',
          borderWidth: 3,
          fill: true,
          tension: 0.4,
          borderDash: [5, 5],
          pointBackgroundColor: colorSuccess,
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(0,0,0,0.8)',
            titleColor: '#fff',
            bodyColor: '#fff',
            borderColor: colorSuccess,
            borderWidth: 1
          }
        },
        scales: {
          y: { 
            beginAtZero: true, 
            ticks: { color: colorHeading, font: { size: 12 } },
            grid: { color: 'rgba(0,0,0,0.1)' }
          },
          x: { 
            ticks: { color: colorHeading, font: { size: 12 } },
            grid: { display: false }
          }
        }
      }
    });
  }
}

function changeEnrollmentPeriod(direction) {
  currentEnrollmentYear += direction;
  if (currentEnrollmentYear < 2023) currentEnrollmentYear = 2023;
  if (currentEnrollmentYear > 2025) currentEnrollmentYear = 2025;
  document.getElementById('enrollmentPeriod').textContent = currentEnrollmentYear;
  updateEnrollmentChart();
}

function changePredictionPeriod(direction) {
  currentPredictionYear += direction;
  if (currentPredictionYear < 2025) currentPredictionYear = 2025;
  if (currentPredictionYear > 2027) currentPredictionYear = 2027;
  document.getElementById('predictionPeriod').textContent = currentPredictionYear;
  updatePredictionChart();
}

function updateEnrollmentChart() {
  if (!enrollmentChart) return;
  const view = document.getElementById('enrollmentView').value;
  const data = enrollmentData[currentEnrollmentYear];
  
  if (view === 'monthly') {
    enrollmentChart.data.labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    enrollmentChart.data.datasets[0].data = data.monthly;
  } else {
    enrollmentChart.data.labels = [currentEnrollmentYear.toString()];
    enrollmentChart.data.datasets[0].data = data.yearly;
  }
  enrollmentChart.update('active');
}

function updatePredictionChart() {
  if (!predictionChart) return;
  const view = document.getElementById('predictionView').value;
  const data = predictionData[currentPredictionYear];
  
  if (view === 'monthly') {
    predictionChart.data.labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    predictionChart.data.datasets[0].data = data.monthly;
  } else {
    predictionChart.data.labels = [currentPredictionYear.toString()];
    predictionChart.data.datasets[0].data = data.yearly;
  }
  predictionChart.update('active');
}

// Animation Script
document.addEventListener('DOMContentLoaded', function() {
  // Initialize charts first
  initializeCharts();
  // Add scroll animation classes
  const animateElements = document.querySelectorAll('#landing .stats-card, #landing .feature-item, #landing .analytics-item, #landing .process-item, #landing .testimonial-card, #landing .accreditation-badge, #landing .enrollment-cta-box');
  
  // Vision Mission scroll animations
  const visionMissionElements = document.querySelectorAll('#landing .scroll-animate-left, #landing .scroll-animate-right');
  
  animateElements.forEach((el, index) => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(30px)';
    el.style.transition = 'all 0.6s ease-out';
    
    if (index % 3 === 0) {
      el.style.transform = 'translateX(-30px)';
    } else if (index % 3 === 2) {
      el.style.transform = 'translateX(30px)';
    }
  });

  // Section titles animate from bottom
  const sectionTitles = document.querySelectorAll('#landing .section-title');
  sectionTitles.forEach(title => {
    title.style.opacity = '0';
    title.style.transform = 'translateY(30px)';
    title.style.transition = 'all 0.6s ease-out';
  });

  // Intersection Observer for animations
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0) translateX(0)';
      } else {
        entry.target.style.opacity = '0';
        const index = [...animateElements, ...sectionTitles].indexOf(entry.target);
        if (index % 3 === 0) {
          entry.target.style.transform = 'translateX(-30px)';
        } else if (index % 3 === 2) {
          entry.target.style.transform = 'translateX(30px)';
        } else {
          entry.target.style.transform = 'translateY(30px)';
        }
      }
    });
  }, {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  });

  // Vision Mission Observer
  const vmObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('animate');
      } else {
        entry.target.classList.remove('animate');
      }
    });
  }, {
    threshold: 0.2,
    rootMargin: '0px 0px -100px 0px'
  });

  // Observe vision mission elements
  visionMissionElements.forEach(el => {
    vmObserver.observe(el);
  });

  // Observe all animation elements
  [...animateElements, ...sectionTitles].forEach(el => {
    observer.observe(el);
  });
});
</script>

<?= $this->endSection() ?>

