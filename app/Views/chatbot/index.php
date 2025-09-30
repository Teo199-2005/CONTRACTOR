<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<!-- Chatbot Header -->
<div class="dashboard-header mb-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1 class="h3 fw-bold text-primary mb-1">FAQ Chatbot</h1>
      <p class="text-muted mb-0 small">Ask about enrollment, schedules, document requirements, and more.</p>
      <p class="text-muted small">Get instant answers to common questions</p>
    </div>
  </div>
  
  <!-- Blue Divider Line -->
  <div class="blue-divider"></div>
</div>

<!-- Chatbot Interface -->
<div class="row">
  <div class="col-lg-8">
    <div class="card bg-white border-0 shadow-sm rounded-3">
      <div class="card-header bg-transparent border-0 p-3">
        <h4 class="card-title mb-0 small">Ask Your Question</h4>
      </div>
      <div class="card-body p-3">
        <div class="input-group mb-3">
          <input id="faqInput" type="text" class="form-control form-control-sm" placeholder="Type your question here...">
          <button id="askBtn" class="btn btn-primary btn-sm">
            <i class="bi bi-search me-1"></i>Ask
          </button>
        </div>
        
        <div id="faqAnswer" class="border rounded p-3 bg-light" style="min-height: 120px;">
          <div class="text-center text-muted py-4">
            <i class="bi bi-chat-dots display-6 text-muted"></i>
            <p class="mt-2 mb-0 small">Your answer will appear here</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-lg-4">
    <div class="card bg-white border-0 shadow-sm rounded-3">
      <div class="card-header bg-transparent border-0 p-3">
        <h4 class="card-title mb-0 small">Quick Questions</h4>
      </div>
      <div class="card-body p-3">
        <div class="d-grid gap-2">
          <button class="btn btn-outline-primary btn-sm text-start quick-btn" onclick="askQuickQuestion('How do I enroll?')">
            <i class="bi bi-person-plus me-2"></i>How do I enroll?
          </button>
          <button class="btn btn-outline-success btn-sm text-start quick-btn" onclick="askQuickQuestion('What documents do I need?')">
            <i class="bi bi-file-earmark-text me-2"></i>Required documents
          </button>
          <button class="btn btn-outline-info btn-sm text-start quick-btn" onclick="askQuickQuestion('When is enrollment?')">
            <i class="bi bi-calendar-event me-2"></i>Enrollment period
          </button>
          <button class="btn btn-outline-warning btn-sm text-start quick-btn" onclick="askQuickQuestion('How do I check grades?')">
            <i class="bi bi-graph-up me-2"></i>Check my grades
          </button>
          <button class="btn btn-outline-secondary btn-sm text-start quick-btn" onclick="askQuickQuestion('What programs do you offer?')">
            <i class="bi bi-mortarboard me-2"></i>Available programs
          </button>
          <button class="btn btn-outline-dark btn-sm text-start quick-btn" onclick="askQuickQuestion('How to contact LPHS?')">
            <i class="bi bi-telephone me-2"></i>Contact information
          </button>
        </div>
      </div>
    </div>
    
    <div class="card bg-white border-0 shadow-sm rounded-3 mt-3">
      <div class="card-header bg-transparent border-0 p-3">
        <h4 class="card-title mb-0 small">Chatbot Tips</h4>
      </div>
      <div class="card-body p-3">
        <ul class="list-unstyled small text-muted mb-0">
          <li class="mb-2"><i class="bi bi-lightbulb text-warning me-2"></i>Be specific with your questions</li>
          <li class="mb-2"><i class="bi bi-lightbulb text-warning me-2"></i>Use keywords like "enrollment", "grades", "schedule"</li>
          <li class="mb-0"><i class="bi bi-lightbulb text-warning me-2"></i>Check the quick questions for common topics</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<style>
.typing-indicator {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #6c757d;
}

.typing-dots {
  display: flex;
  gap: 0.25rem;
}

.typing-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: #6c757d;
  animation: typing 1.4s infinite ease-in-out;
}

.typing-dot:nth-child(1) { animation-delay: -0.32s; }
.typing-dot:nth-child(2) { animation-delay: -0.16s; }
.typing-dot:nth-child(3) { animation-delay: 0s; }

@keyframes typing {
  0%, 80%, 100% { transform: scale(0.8); opacity: 0.5; }
  40% { transform: scale(1); opacity: 1; }
}

.quick-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.badge {
  color: #ffffff !important;
}
</style>

<script>
const input = document.getElementById('faqInput');
const btn = document.getElementById('askBtn');
const box = document.getElementById('faqAnswer');

// LPHS FAQ Database
const faqDatabase = {
  greetings: {
    keywords: ['hi', 'hello', 'hey', 'good morning', 'good afternoon', 'good evening'],
    responses: [
      {
        question: 'Hello there!',
        answer: 'Hi! Welcome to LPHS FAQ Bot. I\'m here to help you with questions about Lourdes Provincial High School. You can ask me about enrollment, grades, schedules, documents, contact information, and more. How can I assist you today?',
        category: 'Greeting'
      }
    ]
  },
  enrollment: {
    keywords: ['enroll', 'enrollment', 'register', 'admission', 'apply'],
    responses: [
      {
        question: 'How do I enroll at LPHS?',
        answer: 'To enroll at LPHS: 1) Create an account on our SMS portal, 2) Fill out the online enrollment form, 3) Upload required documents (birth certificate, report card, good moral certificate), 4) Wait for admin approval. The process is fully digital and available 24/7.',
        category: 'Enrollment'
      },
      {
        question: 'When is the enrollment period?',
        answer: 'Enrollment for School Year 2024-2025 is ongoing. Early enrollment is from January-March, regular enrollment is April-May, and late enrollment extends until June. We recommend enrolling early to secure your slot.',
        category: 'Enrollment'
      }
    ]
  },
  documents: {
    keywords: ['document', 'requirements', 'papers', 'certificate', 'birth certificate', 'report card'],
    responses: [
      {
        question: 'What documents do I need for enrollment?',
        answer: 'Required documents: 1) Birth Certificate (PSA copy), 2) Report Card/Form 138, 3) Good Moral Certificate, 4) Medical Certificate, 5) 2x2 ID Photo. All documents can be uploaded digitally in PDF, JPG, or PNG format.',
        category: 'Requirements'
      }
    ]
  },
  grades: {
    keywords: ['grade', 'grades', 'marks', 'score', 'gwa', 'average'],
    responses: [
      {
        question: 'How do I check my grades?',
        answer: 'To check your grades: 1) Log into the Student Portal, 2) Click "My Grades" in the navigation menu, 3) Select the school year and quarter you want to view. Grades are updated in real-time by your teachers.',
        category: 'Academic'
      }
    ]
  },
  schedule: {
    keywords: ['schedule', 'class', 'time', 'subject', 'timetable'],
    responses: [
      {
        question: 'Where can I find my class schedule?',
        answer: 'Your class schedule is available in the "Materials" section of your Student Portal. It shows your daily subjects, teachers, and time slots from 7:30 AM to 5:00 PM, including the flag ritual at 7:15 AM.',
        category: 'Academic'
      }
    ]
  },
  contact: {
    keywords: ['contact', 'phone', 'email', 'address', 'location'],
    responses: [
      {
        question: 'How can I contact LPHS?',
        answer: 'Contact LPHS: Address: Barangay Lourdes, Panglao Town, Bohol, Philippines | Email: info@lphs.edu.ph | Phone: +63 38 502 9000 | Office Hours: Monday-Friday 7AM-5PM. For urgent concerns, use the SMS portal messaging system.',
        category: 'Contact'
      }
    ]
  },
  fees: {
    keywords: ['fee', 'payment', 'tuition', 'cost', 'price', 'money'],
    responses: [
      {
        question: 'What are the school fees?',
        answer: 'LPHS is a public school under DepEd, so basic education is FREE. However, there may be minimal fees for special programs, materials, or activities. Contact the school office for specific program fees.',
        category: 'Financial'
      }
    ]
  },
  programs: {
    keywords: ['program', 'strand', 'track', 'stem', 'abm', 'humss', 'tvl'],
    responses: [
      {
        question: 'What programs does LPHS offer?',
        answer: 'LPHS offers Senior High School tracks: STEM (Science, Technology, Engineering, Mathematics), ABM (Accountancy, Business, Management), HUMSS (Humanities and Social Sciences), TVL (Technical-Vocational-Livelihood), Arts, and ICT programs.',
        category: 'Academic Programs'
      }
    ]
  }
};

function ask() {
  const q = input.value.trim().toLowerCase();
  if (!q) {
    showAnswer('<span class="text-muted">Please type a question.</span>');
    return;
  }

  // Show typing animation
  showTypingAnimation();
  
  // Simulate realistic response time
  setTimeout(() => {
    const answer = findAnswer(q);
    if (answer) {
      typeAnswer(answer);
    } else {
      typeAnswer({
        question: 'Question not found',
        answer: 'Sorry, I couldn\'t find an answer to your question. Please try using keywords like "enrollment", "grades", "schedule", "documents", or "contact". You can also try the quick questions on the right.',
        category: 'Help'
      });
    }
  }, 1500);
}

function findAnswer(query) {
  for (const category in faqDatabase) {
    const { keywords, responses } = faqDatabase[category];
    if (keywords.some(keyword => query.includes(keyword))) {
      return responses[Math.floor(Math.random() * responses.length)];
    }
  }
  return null;
}

function showTypingAnimation() {
  box.innerHTML = `
    <div class="typing-indicator py-3">
      <i class="bi bi-robot text-primary me-2"></i>
      <span>LPHS Bot is typing</span>
      <div class="typing-dots">
        <div class="typing-dot"></div>
        <div class="typing-dot"></div>
        <div class="typing-dot"></div>
      </div>
    </div>
  `;
}

function typeAnswer(answer) {
  const answerHtml = `
    <div class="answer-content">
      <div class="d-flex align-items-center mb-2">
        <i class="bi bi-robot text-primary me-2"></i>
        <h6 class="text-primary mb-0">${answer.question}</h6>
      </div>
      <div id="typedAnswer" class="mb-2"></div>
      <span class="badge bg-primary text-white small">${answer.category}</span>
    </div>
  `;
  
  box.innerHTML = answerHtml;
  
  // Type the answer character by character
  const typedElement = document.getElementById('typedAnswer');
  let i = 0;
  const text = answer.answer;
  
  function typeChar() {
    if (i < text.length) {
      typedElement.innerHTML += text.charAt(i);
      i++;
      setTimeout(typeChar, 30);
    }
  }
  
  typeChar();
}

function askQuickQuestion(question) {
  input.value = question;
  ask();
}

function showAnswer(content) {
  box.innerHTML = content;
}

btn.addEventListener('click', ask);
input.addEventListener('keydown', e => { if (e.key === 'Enter') ask(); });

// Show welcome message on load
window.addEventListener('load', () => {
  setTimeout(() => {
    typeAnswer({
      question: 'Welcome to LPHS FAQ Bot!',
      answer: 'Hello! I\'m here to help you with questions about Lourdes Provincial High School. You can ask me about enrollment, grades, schedules, requirements, and more. Try typing a question or click one of the quick questions to get started!',
      category: 'Welcome'
    });
  }, 1000);
});
</script>

<?= $this->endSection() ?> 