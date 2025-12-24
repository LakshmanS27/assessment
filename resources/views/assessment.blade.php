<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Assessment</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />
<style>
body { background: linear-gradient(to right, #e0eafc, #cfdef3); min-height: 100vh; font-family: 'Segoe UI', sans-serif; }
.card { border-radius: 15px; }
#instructions ul { padding-left: 1.5rem; }
.question-container {
    background-color: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    min-height: 250px;
    max-width: 900px;
    margin: 0 auto;
}
.form-check-input { width: 22px; height: 22px; cursor: pointer; }
.form-check-input:checked { background-color: #0d6efd; border-color: #0d6efd; }
.form-check-label { cursor: pointer; font-weight: 500; }
#timer { font-weight: bold; font-size: 1.2rem; color: #ff6b6b; }
#reviewPanel { background: #fff; border-radius: 12px; box-shadow: 0 6px 20px rgba(0,0,0,0.08); padding: 25px; margin-bottom: 20px; display: none; }
.card-header { background: linear-gradient(135deg, #6a11cb, #2575fc); color: #fff; }
.btn-gradient { background: linear-gradient(135deg, #ff758c, #ff7eb3); border: none; color: white; }

/* Quiz Nav Buttons */
#quizNav button {
    width: 45px;
    height: 45px;
    font-weight: 600;
    padding: 0;
    border-radius: 6px;
    border: 1px solid #ccc;
    transition: background-color 0.3s ease, border-color 0.3s ease;
    cursor: pointer;
}
/* States */
#quizNav button.current { background-color: white !important; color: black !important; border: 2px solid #0d6efd !important; }
#quizNav button.answered { background-color: #198754 !important; color: white !important; border-color: #198754 !important; }
#quizNav button.not-answered { background-color: #6c757d !important; color: white !important; border-color: #6c757d !important; } /* grey for first time */
#quizNav button.skipped { background-color: #dc3545 !important; color: white !important; border-color: #dc3545 !important; }
</style>
</head>
<body>

<div class="container py-5">
<div class="row justify-content-center">
<div class="col-lg-10 col-md-12">

<form action="{{ route('logout') }}" method="POST" class="text-end mb-3">
@csrf
<button type="submit" class="btn btn-danger btn-sm">Logout</button>
</form>

<div class="card shadow-lg border-0">
<div class="card-header text-center">
<h3 class="mb-0">Assessment</h3>
<p class="mb-0 small">Answer the questions carefully</p>
</div>

<div class="card-body p-4">

<div id="instructions" class="text-center">
<h4 class="mb-3 text-primary">Assessment Instructions</h4>
<ul class="text-start">
<li>20 questions</li>
<li>15 minutes</li>
<li>No refresh</li>
</ul>
<button id="startAssessment" class="btn btn-gradient btn-lg mt-3">Start Assessment</button>
</div>

<div id="reviewPanel">
<h4 class="text-primary mb-3">Review Your Answers</h4>
<table class="table table-bordered" id="reviewTable">
<thead>
<tr><th>Question</th><th>Status</th></tr>
</thead>
<tbody></tbody>
</table>
<div class="d-flex justify-content-between">
<button id="goBackBtn" class="btn btn-secondary">Go Back</button>
<button id="submitBtn" class="btn btn-gradient">Submit</button>
</div>
</div>

<div id="assessmentQuestions" style="display:none;">

    <!-- Quiz Navigation -->
    <div id="quizNav" class="d-flex gap-2 flex-wrap mb-3 justify-content-center"></div>

    <div class="question-container" id="questionContainer"></div>

    <div class="d-flex justify-content-between mt-3">
        <button id="prevBtn" class="btn btn-secondary" disabled>Previous</button>
        <button id="nextBtn" class="btn btn-gradient">Next</button>
    </div>

    <div class="mt-3 text-center">
        Time left: <span id="timer">15:00</span>
    </div>

</div>

</div>
</div>
</div>
</div>
</div>

<script>
const questions = @json($questions);

let currentIndex = 0;
let answers = {};
let visited = {}; // tracks if user opened question
let timer, autoSaveTimer;
let timeLeft = 15 * 60;

const instructionsDiv = document.getElementById('instructions');
const assessmentDiv = document.getElementById('assessmentQuestions');
const questionContainer = document.getElementById('questionContainer');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
const timerSpan = document.getElementById('timer');
const reviewPanel = document.getElementById('reviewPanel');
const reviewTableBody = document.querySelector('#reviewTable tbody');
const goBackBtn = document.getElementById('goBackBtn');
const submitBtn = document.getElementById('submitBtn');
const quizNav = document.getElementById('quizNav');

document.getElementById('startAssessment').onclick = () => {
    instructionsDiv.style.display = 'none';
    assessmentDiv.style.display = 'block';
    initQuizNav();
    renderQuestion();
    startTimer();
    startAutoSave();
};

// Initialize quiz navigation buttons
function initQuizNav() {
    quizNav.innerHTML = '';
    questions.forEach((q, i) => {
        const btn = document.createElement('button');
        btn.textContent = i + 1;
        btn.classList.add('btn','btn-sm','not-answered'); // initial grey

        btn.onclick = () => {
            saveAnswer();
            visited[currentIndex] = true;
            currentIndex = i;
            renderQuestion();
        };

        quizNav.appendChild(btn);
    });
    updateQuizNav();
}

function updateQuizNav() {
    const buttons = quizNav.querySelectorAll('button');
    buttons.forEach((btn, i) => {
        btn.classList.remove('current','answered','not-answered','skipped');

        const qid = questions[i].id;
        if (i === currentIndex) {
            btn.classList.add('current');
        } else if (answers[qid] && answers[qid].length > 0) {
            btn.classList.add('answered');
        } else if (visited[i]) {
            btn.classList.add('skipped');
        } else {
            btn.classList.add('not-answered');
        }
    });
}

function renderQuestion() {
    visited[currentIndex] = true;
    const q = questions[currentIndex];
    const saved = answers[q.id] ?? '';
    let html = `<p class="fw-bold">Question ${currentIndex+1}</p><p>${q.question_text}</p>`;

    if (q.question_type === 'text') {
        html += `<input id="answerInput" class="form-control" value="${saved}">`;
    } else if (q.question_type === 'multiple_choice') {
        let opts = Array.isArray(q.options) ? q.options : JSON.parse(q.options || '[]');
        opts.forEach(opt => {
            html += `<div class="form-check">
            <input class="form-check-input" type="radio" name="answer" value="${opt}" ${saved===opt?'checked':''}>
            <label class="form-check-label">${opt}</label>
            </div>`;
        });
    }

    questionContainer.innerHTML = html;
    prevBtn.disabled = currentIndex === 0;
    nextBtn.textContent = currentIndex === questions.length-1 ? 'Finish' : 'Next';

    updateQuizNav();
}

function saveAnswer() {
    const q = questions[currentIndex];
    const text = document.getElementById('answerInput');
    const radio = document.querySelector('input[name="answer"]:checked');
    answers[q.id] = text ? text.value.trim() : (radio ? radio.value : '');
}

prevBtn.onclick = () => {
    saveAnswer();
    if(currentIndex > 0){
        currentIndex--;
        renderQuestion();
    }
};
nextBtn.onclick = () => {
    saveAnswer();
    if (currentIndex < questions.length-1) {
        currentIndex++;
        renderQuestion();
    } else showReviewPanel();
};

function showReviewPanel() {
    reviewTableBody.innerHTML = '';
    questions.forEach((q,i) => {
        reviewTableBody.innerHTML += `<tr><td>Q${i+1}</td><td>${answers[q.id] ? 'Answered' : 'Not Answered'}</td></tr>`;
    });
    assessmentDiv.style.display = 'none';
    reviewPanel.style.display = 'block';
}

goBackBtn.onclick = () => {
    reviewPanel.style.display = 'none';
    assessmentDiv.style.display = 'block';
    renderQuestion();
};

function startTimer() {
    timer = setInterval(() => {
        timeLeft--;
        timerSpan.textContent =
            String(Math.floor(timeLeft/60)).padStart(2,'0') + ':' +
            String(timeLeft%60).padStart(2,'0');
        if (timeLeft <= 0) submitAssessment();
    }, 1000);
}

function startAutoSave() {
    autoSaveTimer = setInterval(() => {
        saveAnswer();
        fetch("{{ route('assessment.autosave') }}", {
            method: 'POST',
            headers: {
                'Content-Type':'application/json',
                'X-CSRF-TOKEN':'{{ csrf_token() }}'
            },
            body: JSON.stringify({ answers })
        });
    }, 30000);
}

submitBtn.onclick = submitAssessment;

function submitAssessment() {
    clearInterval(timer);
    clearInterval(autoSaveTimer);

    fetch("{{ route('assessment.submit') }}", {
        method: 'POST',
        headers: {
            'Content-Type':'application/json',
            'X-CSRF-TOKEN':'{{ csrf_token() }}'
        },
        body: JSON.stringify({ answers })
    }).then(() => {
        window.location.href = "{{ route('assessment.results') }}";
    });
}
</script>

</body>
</html>
