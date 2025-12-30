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
.card { border-radius: 16px; }
.card-header { background: linear-gradient(135deg, #6a11cb, #2575fc); }
.question-box, .palette-box { background: #fff; border-radius: 14px; padding: 30px; box-shadow: 0 6px 20px rgba(0,0,0,0.08); }
#quizNav button { height: 42px; font-weight: 600; }
#timer { font-size: 1.1rem; font-weight: 700; color: #dc3545; }
#violationWarning {
    position: fixed; top: 10px; left: 50%; transform: translateX(-50%);
    background: #ffc107; color: #000; padding: 10px 20px; border-radius: 8px;
    display: none; z-index: 9999; font-weight: 600;
}
</style>
</head>
<body>

<div id="violationWarning"></div>

<div class="container py-5">

    <!-- Logout -->
    <form action="{{ route('logout') }}" method="POST" class="text-end mb-3">
        @csrf
        <button type="submit" class="btn btn-danger btn-sm">Logout</button>
    </form>

    <div class="card shadow-lg border-0">
        <div class="card-header text-center text-white d-flex align-items-center justify-content-center py-3">
    <img src="{{ asset('images/logo_sq.png') }}" alt="Logo" width="40" height="40" class="me-3">
    <div>
        <h4 class="mb-0">Assessment</h4>
        <small>Answer the questions carefully</small>
    </div>
</div>

        <div class="card-body p-4">

            <!-- Instructions -->
            <div id="instructions" class="text-center">
                <h5 class="text-primary mb-3">Assessment Instructions</h5>
                <ul class="text-start mb-3 ps-3">
                    <li>The assessment will comprise <strong>MCQs</strong> and <strong>Text Input</strong> questions.</li>
                    <li>Total Questions: <strong>20</strong>.</li>
                    <li>Duration: <strong>15 minutes</strong>.</li>
                    <li>All questions are <strong>compulsory</strong> and carry <strong>1 mark</strong> each.</li>
                    <li>No negative marking for wrong answers.</li>
                    <li>Questions and options are randomized per user.</li>
                    <li>Answers can be changed anytime and are saved automatically.</li>
                    <li>Review answered and unanswered questions anytime.</li>
                    <li>Assessment auto-closes on time expiry or via "Finish and Submit".</li>
                    <li>Navigation via buttons or navigation panel only.</li>
                    <li>Time starts on pressing <strong>Start Assessment</strong>.</li>
                    <li>No page refresh or back navigation allowed.</li>
                </ul>

                <h5 class="text-danger mb-3">Important Warnings</h5>
                <ul class="text-start mb-3 ps-3">
                    <li>Right-click disabled.</li>
                    <li>Copy-paste blocked.</li>
                    <li>Keyboard shortcuts (Ctrl+C, Ctrl+V, Ctrl+X) blocked.</li>
                    <li>Switching tabs/window is detected.</li>
                    <li>Violations will trigger warnings.</li>
                    <li>Max violations or time expiry will auto-submit the assessment.</li>
                </ul>

                <div class="mt-3">
                    <button id="startAssessment" class="btn btn-primary btn-lg">Start Assessment</button>
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

            <!-- Assessment Questions -->
            <div id="assessmentQuestions" class="d-none">
                <div class="row g-4">
                    <div class="col-md-9">
                        <div class="question-box">
                            <div id="questionContainer"></div>
                            <div class="d-flex justify-content-between mt-4">
                                <button id="prevBtn" class="btn btn-outline-secondary" disabled>Previous</button>
                                <button id="nextBtn" class="btn btn-primary">Next</button>
                            </div>
                            <div class="text-center mt-3">
                                Time left: <span id="timer">15:00</span>
                            </div>
                        </div>
                    </div>
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

<!-- ----------------- JS ----------------- -->
<script>
document.addEventListener('DOMContentLoaded', function() {

    let questions = @json($questions);

    // --- Session/Local Storage Setup ---
    if (!sessionStorage.getItem('assessment_questions')) {
        sessionStorage.setItem('assessment_questions', JSON.stringify(questions));
    } else {
        questions = JSON.parse(sessionStorage.getItem('assessment_questions'));
    }

    let currentIndex = parseInt(localStorage.getItem('currentIndex')) || 0;
    let answers = JSON.parse(localStorage.getItem('answers')) || @json($savedAnswers ?? []);
    let visited = JSON.parse(localStorage.getItem('visited')) || {};
    let timer;
    let timeLeft = parseInt(localStorage.getItem('timeLeft')) || @json($timeLeft);
    let violations = Number(localStorage.getItem('violations'));
    if (isNaN(violations)) violations = 0;
    let assessmentStarted = localStorage.getItem('assessmentStarted') === 'true';
    const maxViolations = 3;

    // --- DOM Elements ---
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

    // --- Timer Display ---
    function updateTimerDisplay() {
        const m = String(Math.floor(timeLeft / 60)).padStart(2, '0');
        const s = String(timeLeft % 60).padStart(2, '0');
        timerSpan.textContent = `${m}:${s}`;
    }
    updateTimerDisplay();

    // --- Start Assessment ---
    // Reset violations when assessment starts
function startAssessmentSession() {
    violations = 0;
    localStorage.setItem('violations', violations);
    instructionsDiv.classList.add('d-none');
    assessmentDiv.classList.remove('d-none');
    assessmentStarted = true;
    localStorage.setItem('assessmentStarted','true');
    initQuizNav();
    renderQuestion();
    startTimer();
    initViolations();
    window.history.pushState(null, null, window.location.href);
    window.onpopstate = () => autoSubmitAssessment();
}

    document.getElementById('startAssessment').onclick = startAssessmentSession;

    // --- Resume Assessment ---
    if (assessmentStarted) {
        instructionsDiv.classList.add('d-none');
        assessmentDiv.classList.remove('d-none');
        initQuizNav();
        renderQuestion();
        startTimer();
        initViolations();
    }

    // --- Violations ---
    function initViolations() {
        if(!assessmentStarted) return;
        document.addEventListener('contextmenu', e => { e.preventDefault(); handleViolation('Right-click is disabled!'); });
        ['copy','paste','cut'].forEach(ev => document.addEventListener(ev, e => { e.preventDefault(); handleViolation('Copy-paste is disabled!'); }));
        document.addEventListener('keydown', e => {
            if((e.ctrlKey || e.metaKey) && ['c','v','x','a'].includes(e.key.toLowerCase())) { e.preventDefault(); handleViolation('Keyboard shortcuts are disabled!'); }
        });
        let lastBlur = 0;
        window.addEventListener('blur', () => {
            const now = Date.now();
            if(now - lastBlur > 1000){
                handleViolation('Switching tabs is not allowed!');
                lastBlur = now;
            }
        });
    }

    function handleViolation(msg){
        violations++;
        localStorage.setItem('violations', violations);
        violationWarning.textContent = `Warning ${violations}: ${msg}`;
        violationWarning.style.display = 'block';
        violationWarning.style.background = '#ffc107';
        setTimeout(() => violationWarning.style.display = 'none', 3000);
        if(violations >= maxViolations) autoSubmitAssessment();
    }

    // --- Timer ---
    function startTimer() {
        timer = setInterval(() => {
            timeLeft--;
            if(timeLeft < 0) timeLeft = 0;
            localStorage.setItem('timeLeft', timeLeft);
            updateTimerDisplay();
            if(timeLeft % 30 === 0) autosaveToBackend();
            if(timeLeft <= 0){
                clearInterval(timer);
                submitAssessment(true);
            }
        }, 1000);
    }

    function autosaveToBackend() {
        fetch("{{ route('assessment.autosave') }}", {
            method:'POST',
            headers:{
                'Content-Type':'application/json',
                'X-CSRF-TOKEN':'{{ csrf_token() }}'
            },
            body: JSON.stringify({answers:answers, time_left:timeLeft})
        });
    }

    // --- Quiz Navigation ---
    function initQuizNav() {
        quizNav.innerHTML = '';
        questions.forEach((q,i)=>{
            const btn = document.createElement('button');
            btn.textContent = i+1;
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

    function updateQuizNav(){
        quizNav.querySelectorAll('button').forEach((btn,i)=>{
            const qid = questions[i].id;
            btn.className='btn btn-sm mx-1 mb-1';
            if(i === currentIndex) btn.classList.add('btn-light','border-primary','text-primary');
            else if(answers[qid]) btn.classList.add('btn-success','text-white');
            else if(visited[i]) btn.classList.add('btn-danger','text-white');
            else btn.classList.add('btn-secondary','text-white');
        });
    }

    // --- Render Question ---
    function renderQuestion() {
        visited[currentIndex] = true;
        localStorage.setItem('visited', JSON.stringify(visited));
        const q = questions[currentIndex];
        const saved = answers[q.id] || '';
        let html = `<p class="fw-bold">Question ${currentIndex+1}</p><p>${q.question_text}</p>`;
        if(q.question_type === 'text'){
            html += `<input id="answerInput" class="form-control" value="${saved}" oninput="saveAnswer()">`;
        } else {
            const opts = Array.isArray(q.options) ? q.options : JSON.parse(q.options||'[]');
            opts.forEach(opt => {
                const checked = String(saved) === String(opt) ? 'checked' : '';
                html += `<div class="form-check">
                            <input class="form-check-input" type="radio" name="answer" value="${opt}" ${checked} onchange="saveAnswer()">
                            <label class="form-check-label">${opt}</label>
                        </div>`;
            });
        }
        questionContainer.innerHTML = html;
        prevBtn.disabled = currentIndex === 0;
        nextBtn.textContent = currentIndex === questions.length - 1 ? 'Finish' : 'Next';
        updateQuizNav();
    }

    // --- Save Answer ---
    function saveAnswer(){
        const q = questions[currentIndex];
        const text = document.getElementById('answerInput');
        const radio = document.querySelector('input[name="answer"]:checked');
        answers[q.id] = text ? text.value.trim() : (radio ? radio.value : '');
        localStorage.setItem('answers', JSON.stringify(answers));
    }

    // --- Prev/Next Buttons ---
    prevBtn.onclick = () => { saveAnswer(); if(currentIndex>0){ currentIndex--; localStorage.setItem('currentIndex', currentIndex); renderQuestion(); }};
    nextBtn.onclick = () => { saveAnswer(); if(currentIndex<questions.length-1){ currentIndex++; localStorage.setItem('currentIndex', currentIndex); renderQuestion(); } else { showReviewPanel(); }};

    // --- Review Panel ---
    function showReviewPanel(){
        reviewTableBody.innerHTML = '';
        questions.forEach((q,i)=>{
            reviewTableBody.innerHTML += `<tr><td>Q${i+1}</td><td>${answers[q.id]?'Answered':'Not Answered'}</td></tr>`;
        });
        assessmentDiv.classList.add('d-none');
        reviewPanel.classList.remove('d-none');
    }

    document.getElementById('goBackBtn').onclick = () => { reviewPanel.classList.add('d-none'); assessmentDiv.classList.remove('d-none'); renderQuestion(); };

    // --- Clear Storage ---
    function clearStorage(){
        ['timeLeft','answers','currentIndex','visited','assessmentStarted','violations'].forEach(k=>localStorage.removeItem(k));
        sessionStorage.removeItem('assessment_questions');
    }

    // --- Submit Assessment ---
    // Submit assessment
function submitAssessment(auto=false){
    saveAnswer();
    clearInterval(timer);
    const finalAnswers = answers;
    clearStorage();

    fetch("{{ route('assessment.submit') }}",{
        method:'POST',
        headers:{
            'Content-Type':'application/json',
            'X-CSRF-TOKEN':'{{ csrf_token() }}'
        },
        body: JSON.stringify({
            answers: finalAnswers,
            violations: violations
        })
    }).then(res => {
        // Redirect to results page after submit
        window.location.href = "{{ route('assessment.results') }}";
    }).catch(err => {
        alert('Error submitting assessment.');
    });
}

    // --- Auto Submit on Violations ---
    // Auto-submit function
function autoSubmitAssessment(){
    violationWarning.textContent='Maximum violations reached! Submitting...';
    violationWarning.style.background='#dc3545';
    violationWarning.style.display='block';
    submitAssessment(true);
}

    // --- Manual Submit ---
    // Manual submit from review panel
    document.getElementById('submitBtn').onclick = () => submitAssessment();

});
</script>

</body>
</html>
