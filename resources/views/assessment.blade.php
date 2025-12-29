<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Assessment</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg, #e0eafc, #cfdef3);
    font-family: 'Segoe UI', sans-serif;
}

/* Main Card */
.card {
    border-radius: 16px;
}

/* Header */
.card-header {
    background: linear-gradient(135deg, #6a11cb, #2575fc);
}

/* Question Panel */
.question-box {
    background: #fff;
    border-radius: 14px;
    padding: 30px;
    min-height: 260px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
}

/* Palette */
.palette-box {
    background: #fff;
    border-radius: 14px;
    padding: 20px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
}

/* Quiz Nav Buttons */
#quizNav button {
    height: 42px;
    font-weight: 600;
}

/* Timer */
#timer {
    font-size: 1.1rem;
    font-weight: 700;
    color: #dc3545;
}

/* Violation Warning */
#violationWarning {
    position: fixed;
    top: 10px;
    left: 50%;
    transform: translateX(-50%);
    background: #ffc107;
    color: #000;
    padding: 10px 20px;
    border-radius: 8px;
    display: none;
    z-index: 9999;
    font-weight: 600;
}
</style>
</head>
<body>

<div id="violationWarning"></div>

<div class="container py-5">

<form action="{{ route('logout') }}" method="POST" class="text-end mb-3">
    @csrf
    <button type="submit" class="btn btn-danger btn-sm">Logout</button>
</form>

<div class="card shadow-lg border-0">
    <div class="card-header text-center text-white">
        <h4 class="mb-0">Assessment</h4>
        <small>Answer the questions carefully</small>
    </div>

    <div class="card-body p-4">

<!-- Instructions -->
<div id="instructions" class="text-center">
    <h5 class="text-primary mb-3">Assessment Instructions</h5>
<ul class="text-start mb-3 ps-3">
    <li>The assessment will comprise of <strong>Objective type Multiple Choice Questions (MCQs)</strong> and <strong>Text Input type Q/A</strong>.</li>
    <li>Total Questions to be attended are <strong>20</strong>.</li>
    <li>Duration of the assessment: <strong>15 minutes</strong>.</li>
    <li>All questions are <strong>compulsory</strong> and each carries <strong>one mark</strong>.</li>
    <li>There will be <strong>no negative marking</strong> for wrong answers.</li>
    <li>Each student will receive questions and answers in a <strong>different order</strong>, selected randomly from a <strong>fixed question databank</strong>.</li>
    <li>The assessment does not require using any <strong>paper, pen, pencil, or calculator</strong>.</li>
    <li>The answers can be <strong>changed at any time</strong> during the assessment and are <strong>saved automatically</strong>.</li>
    <li>It is possible to <strong>review both answered and unanswered questions</strong> at any time.</li>
    <li>The assessment will <strong>automatically close</strong> when the time limit is over.  
        If the examinee finishes early, they can quit by pressing the <strong>“Finish and Submit” button</strong>.</li>
    <li>The examinee can navigate to the <strong>first, last, previous, next, or any unanswered question</strong> using the buttons on screen or the navigation bar throughout the assessment.</li>
    <li>The <strong>time of the assessment begins only when the “Start Assessment” button</strong> is pressed.</li>
    <li><strong>No page refresh or back navigation</strong> is allowed.</li>
</ul>

<h5 class="text-danger mb-3">Important Warnings</h5>
<ul class="text-start mb-3 ps-3">
    <li><strong>Right-click</strong> is disabled during the assessment.</li>
    <li><strong>Copying and pasting</strong> content is blocked.</li>
    <li><strong>Keyboard shortcuts</strong> (e.g., Ctrl+C, Ctrl+V, Ctrl+X) are disabled.</li>
    <li>Switching tabs or leaving the window will be <strong>detected</strong>.</li>
    <li><strong>Warnings</strong> will be issued for any violations of these rules.</li>
    <li>The assessment will be <strong>auto-submitted</strong> if the time expires or if violations occur repeatedly.</li>
</ul>

<div class="mt-3">
    <button id="startAssessment" class="btn btn-primary btn-lg">
        Start Assessment
    </button>
</div>
</div>

<!-- Review Panel -->
<div id="reviewPanel" class="d-none">
    <h5 class="text-primary mb-3">Review Answers</h5>
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Question</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody id="reviewTableBody"></tbody>
    </table>

    <div class="d-flex justify-content-between">
        <button id="goBackBtn" class="btn btn-secondary">Go Back</button>
        <button id="submitBtn" class="btn btn-success">Submit</button>
    </div>
</div>

<!-- Assessment -->
<div id="assessmentQuestions" class="d-none">
    <div class="row g-4">
        <!-- LEFT -->
        <div class="col-md-9">
            <div class="question-box">
                <div id="questionContainer"></div>

                <div class="d-flex justify-content-between mt-4">
                    <button id="prevBtn" class="btn btn-outline-secondary" disabled>
                        Previous
                    </button>
                    <button id="nextBtn" class="btn btn-primary">
                        Next
                    </button>
                </div>

                <div class="text-center mt-3">
                    Time left: <span id="timer">15:00</span>
                </div>
            </div>
        </div>

        <!-- RIGHT -->
        <div class="col-md-3">
            <div class="palette-box position-sticky top-0">
                <h6 class="text-center fw-bold mb-3">Navigation Panel</h6>
                <div id="quizNav" class="d-grid gap-2 mb-4" style="grid-template-columns: repeat(4,1fr);"></div>
                <div class="small">
                    <div class="mb-1"><span class="badge bg-success me-2">&nbsp;</span> Answered</div>
                    <div class="mb-1"><span class="badge bg-secondary me-2">&nbsp;</span> Not Attempted</div>
                    <div class="mb-1"><span class="badge bg-danger me-2">&nbsp;</span> Skipped</div>
                    <div><span class="badge bg-light border border-primary me-2">&nbsp;</span> Current</div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
</div>
</div>

<script>
// --- Initialization & Data Persistence ---
let questions = @json($questions);

// Check if we have a saved version of questions to prevent re-shuffling on refresh
const savedQuestions = sessionStorage.getItem('assessment_questions');
if (savedQuestions) {
    questions = JSON.parse(savedQuestions);
} else {
    // Save the initial set of questions provided by the server
    sessionStorage.setItem('assessment_questions', JSON.stringify(questions));
}

let currentIndex = parseInt(localStorage.getItem('currentIndex')) || 0;
let answers = JSON.parse(localStorage.getItem('answers')) || {};
let visited = JSON.parse(localStorage.getItem('visited')) || {};
let timer;

// Timer Logic: Check for saved time, otherwise default to 15 mins
const totalInitialTime = 15 * 60;
let timeLeft = localStorage.getItem('timeLeft') ? parseInt(localStorage.getItem('timeLeft')) : totalInitialTime;

// IMMEDIATELY update the display to prevent seeing "15:00"
function updateTimerDisplay() {
    const mins = String(Math.floor(timeLeft / 60)).padStart(2, '0');
    const secs = String(timeLeft % 60).padStart(2, '0');
    document.getElementById('timer').textContent = `${mins}:${secs}`;
}
updateTimerDisplay(); // Run this right away

const instructionsDiv = document.getElementById('instructions');
const assessmentDiv = document.getElementById('assessmentQuestions');
const questionContainer = document.getElementById('questionContainer');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
const timerSpan = document.getElementById('timer');
const reviewPanel = document.getElementById('reviewPanel');
const reviewTableBody = document.getElementById('reviewTableBody');
const quizNav = document.getElementById('quizNav');
const violationWarning = document.getElementById('violationWarning');

let violations = parseInt(localStorage.getItem('violations')) || 0;
const maxViolations = 3;
let lastViolationTime = 0;
const violationCooldown = 500;
let assessmentStarted = localStorage.getItem('assessmentStarted') === 'true';

// --- Logic for Page Load ---
window.onload = () => {
    if (assessmentStarted) {
        // Resume assessment automatically if it was already started
        instructionsDiv.classList.add('d-none');
        assessmentDiv.classList.remove('d-none');
        initQuizNav();
        renderQuestion();
        startTimer();
        initViolations();

        // Prevent browser back button
        window.history.pushState(null, null, window.location.href);
        window.onpopstate = function(event) {
            autoSubmitAssessment();
        };
    }
};


// Start assessment
document.getElementById('startAssessment').onclick = () => {
    instructionsDiv.classList.add('d-none');
    assessmentDiv.classList.remove('d-none');
    assessmentStarted = true;
    localStorage.setItem('assessmentStarted', 'true');
    initQuizNav();
    renderQuestion();
    startTimer();
    initViolations();

    // Prevent browser back button
    window.history.pushState(null, null, window.location.href);
    window.onpopstate = function(event) {
        autoSubmitAssessment();
    };
};


function initViolations() {
    document.addEventListener('contextmenu', e => {
        if (!assessmentStarted) return;
        e.preventDefault();
        handleViolation('Right-click is disabled!');
    });

    ['copy', 'paste', 'cut'].forEach(ev => {
        document.addEventListener(ev, e => {
            if (!assessmentStarted) return;
            e.preventDefault();
            handleViolation('Copying and pasting is disabled!');
        });
    });

    document.addEventListener('keydown', e => {
        if (!assessmentStarted) return;
        if ((e.ctrlKey || e.metaKey) && ['c', 'v', 'x', 'a'].includes(e.key.toLowerCase())) {
            e.preventDefault();
            handleViolation('Keyboard shortcuts are disabled!');
        }
    });

    window.addEventListener('blur', () => {
        if (!assessmentStarted) return;
        handleViolation('Switching tabs or leaving the window is not allowed!');
    });
}

function handleViolation(message) {
    const now = Date.now();
    if (now - lastViolationTime < violationCooldown) return;
    lastViolationTime = now;
    violations++;
    localStorage.setItem('violations', violations); // Persist violation count
    
    showViolation(`Warning ${violations}: ${message}`);
    if (violations >= maxViolations) autoSubmitAssessment();
}

function showViolation(message) {
    violationWarning.textContent = message;
    violationWarning.style.display = 'block';
    setTimeout(() => violationWarning.style.display = 'none', 3000);
}

// Timer with Persistence
function startTimer() {
    timer = setInterval(() => {
        timeLeft--;
        localStorage.setItem('timeLeft', timeLeft); // Save time every second
        
        const mins = String(Math.floor(timeLeft / 60)).padStart(2, '0');
        const secs = String(timeLeft % 60).padStart(2, '0');
        timerSpan.textContent = `${mins}:${secs}`;

        if (timeLeft <= 0) {
            clearInterval(timer);
            submitAssessment(true);
        }
    }, 1000);
}

function initQuizNav() {
    quizNav.innerHTML = '';
    questions.forEach((q, i) => {
        const btn = document.createElement('button');
        btn.textContent = i + 1;
        btn.className = 'btn btn-sm btn-secondary mx-1 mb-1';
        btn.onclick = () => { 
            saveAnswer(); 
            visited[currentIndex] = true; 
            currentIndex = i; 
            localStorage.setItem('currentIndex', currentIndex);
            renderQuestion(); 
        };
        quizNav.appendChild(btn);
    });
    updateQuizNav();
}

function updateQuizNav() {
    quizNav.querySelectorAll('button').forEach((btn, i) => {
        const qid = questions[i].id;
        btn.className = 'btn btn-sm mx-1 mb-1';
        if (i === currentIndex) btn.classList.add('btn-light', 'border-primary', 'text-primary');
        else if (answers[qid]) btn.classList.add('btn-success', 'text-white');
        else if (visited[i]) btn.classList.add('btn-danger', 'text-white');
        else btn.classList.add('btn-secondary', 'text-white');
    });
}

function renderQuestion() {
    visited[currentIndex] = true;
    localStorage.setItem('visited', JSON.stringify(visited));
    
    const q = questions[currentIndex];
    const saved = answers[q.id] || '';
    let html = `<p class="fw-bold">Question ${currentIndex + 1}</p><p>${q.question_text}</p>`;
    
    if (q.question_type === 'text') {
        html += `<input id="answerInput" class="form-control" value="${saved}" oninput="saveAnswer()">`;
    } else {
        const opts = Array.isArray(q.options) ? q.options : JSON.parse(q.options || '[]');
        opts.forEach(opt => {
            html += `<div class="form-check">
                        <input class="form-check-input" type="radio" name="answer" value="${opt}" 
                        ${saved === opt ? 'checked' : ''} onchange="saveAnswer()">
                        <label class="form-check-label">${opt}</label>
                     </div>`;
        });
    }
    questionContainer.innerHTML = html;
    prevBtn.disabled = currentIndex === 0;
    nextBtn.textContent = currentIndex === questions.length - 1 ? 'Finish' : 'Next';
    updateQuizNav();
}

function saveAnswer() {
    const q = questions[currentIndex];
    const text = document.getElementById('answerInput');
    const radio = document.querySelector('input[name="answer"]:checked');
    answers[q.id] = text ? text.value.trim() : (radio ? radio.value : '');
    localStorage.setItem('answers', JSON.stringify(answers));
}

prevBtn.onclick = () => { 
    saveAnswer(); 
    if(currentIndex > 0) { 
        currentIndex--; 
        localStorage.setItem('currentIndex', currentIndex);
        renderQuestion(); 
    } 
};

nextBtn.onclick = () => { 
    saveAnswer(); 
    if(currentIndex < questions.length - 1) { 
        currentIndex++; 
        localStorage.setItem('currentIndex', currentIndex);
        renderQuestion(); 
    } else { 
        showReviewPanel(); 
    } 
};

function showReviewPanel() {
    reviewTableBody.innerHTML = '';
    questions.forEach((q, i) => { 
        reviewTableBody.innerHTML += `<tr><td>Q${i+1}</td><td>${answers[q.id] ? 'Answered' : 'Not Answered'}</td></tr>`; 
    });
    assessmentDiv.classList.add('d-none');
    reviewPanel.classList.remove('d-none');
}

document.getElementById('goBackBtn').onclick = () => { 
    reviewPanel.classList.add('d-none'); 
    assessmentDiv.classList.remove('d-none'); 
    renderQuestion(); 
};

function clearStorage() {
    localStorage.removeItem('timeLeft');
    localStorage.removeItem('answers');
    localStorage.removeItem('currentIndex');
    localStorage.removeItem('visited');
    localStorage.removeItem('assessmentStarted');
    localStorage.removeItem('violations');
    sessionStorage.removeItem('assessment_questions');
}

function submitAssessment(auto = false) {
    saveAnswer();
    clearInterval(timer);
    const finalAnswers = answers;
    
    // Clear storage so the next attempt starts fresh
    clearStorage();

    fetch("{{ route('assessment.submit') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ answers: finalAnswers })
    }).finally(() => {
        window.location.href = "{{ route('assessment.results') }}";
    });
}

function autoSubmitAssessment() {
    saveAnswer();
    clearInterval(timer);
    violationWarning.textContent = 'Maximum violations reached! Submitting...';
    violationWarning.style.background = '#dc3545';
    violationWarning.style.display = 'block';
    
    const finalAnswers = answers;
    clearStorage();

    fetch("{{ route('assessment.submit') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ answers: finalAnswers })
    }).finally(() => window.location.href = "{{ route('assessment.results') }}");
}

document.getElementById('submitBtn').onclick = () => submitAssessment();
</script>

</body>
</html>
