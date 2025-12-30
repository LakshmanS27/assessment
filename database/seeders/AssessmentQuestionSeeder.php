<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AssessmentQuestion;

class AssessmentQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            // Python
            ['question_text' => 'What does the `len()` function do in Python?', 'question_type' => 'text', 'correct_answer' => 'Returns the number of items in an object'],
            ['question_text' => 'Which keyword is used to define a function in Python?', 'question_type' => 'multiple_choice', 'options' => ['func', 'def', 'function', 'lambda'], 'correct_answer' => 'def'],
            ['question_text' => 'Python supports which type of polymorphism?', 'question_type' => 'text', 'correct_answer' => 'Runtime polymorphism'],

            // Java
            ['question_text' => 'Which keyword is used to inherit a class in Java?', 'question_type' => 'multiple_choice', 'options' => ['implement', 'extends', 'inherits', 'super'], 'correct_answer' => 'extends'],
            ['question_text' => 'What is the default value of an uninitialized int variable in Java?', 'question_type' => 'text', 'correct_answer' => '0'],
            ['question_text' => 'Which loop is guaranteed to execute at least once?', 'question_type' => 'multiple_choice', 'options' => ['for', 'while', 'do-while', 'foreach'], 'correct_answer' => 'do-while'],

            // PHP
            ['question_text' => 'Which symbol is used to declare a variable in PHP?', 'question_type' => 'multiple_choice', 'options' => ['#', '$', '@', '%'], 'correct_answer' => '$'],
            ['question_text' => 'PHP is a ___ language?', 'question_type' => 'text', 'correct_answer' => 'Server-side scripting'],
            ['question_text' => 'Which function outputs text in PHP?', 'question_type' => 'multiple_choice', 'options' => ['echo', 'print', 'write', 'display'], 'correct_answer' => 'echo'],

            // Laravel
            ['question_text' => 'Laravel follows which architectural pattern?', 'question_type' => 'text', 'correct_answer' => 'MVC'],
            ['question_text' => 'Which command creates a new controller in Laravel?', 'question_type' => 'multiple_choice', 'options' => ['php artisan make:controller', 'php artisan new:controller', 'php artisan generate:controller', 'php artisan create:controller'], 'correct_answer' => 'php artisan make:controller'],
            ['question_text' => 'Which file is used for database configuration in Laravel?', 'question_type' => 'text', 'correct_answer' => '.env'],

            // Django
            ['question_text' => 'Django is a framework for which language?', 'question_type' => 'multiple_choice', 'options' => ['Python','Java','PHP','JavaScript'], 'correct_answer' => 'Python'],
            ['question_text' => 'Which file contains URL mappings in Django?', 'question_type' => 'text', 'correct_answer' => 'urls.py'],
            ['question_text' => 'Which command is used to create a new Django project?', 'question_type' => 'multiple_choice', 'options' => ['django startproject', 'django create', 'python manage.py startproject', 'django new'], 'correct_answer' => 'python manage.py startproject'],

            // Machine Learning
            ['question_text' => 'What is overfitting in Machine Learning?', 'question_type' => 'text', 'correct_answer' => 'Model performs well on training data but poorly on unseen data'],
            ['question_text' => 'Which of these is a supervised learning algorithm?', 'question_type' => 'multiple_choice', 'options' => ['Linear Regression','K-Means','DBSCAN','PCA'], 'correct_answer' => 'Linear Regression'],
            ['question_text' => 'Which metric is commonly used for classification problems?', 'question_type' => 'text', 'correct_answer' => 'Accuracy'],

            // FastAPI
            ['question_text' => 'FastAPI is built on top of which Python library?', 'question_type' => 'multiple_choice', 'options' => ['Flask','Starlette','Django','Tornado'], 'correct_answer' => 'Starlette'],
            ['question_text' => 'Which HTTP method is used to create a new resource in FastAPI?', 'question_type' => 'text', 'correct_answer' => 'POST'],
            ['question_text' => 'Which decorator is used to define an endpoint in FastAPI?', 'question_type' => 'multiple_choice', 'options' => ['@app.route', '@app.get', '@app.endpoint', '@app.method'], 'correct_answer' => '@app.get'],

            // Docker
            ['question_text' => 'Which command is used to build a Docker image?', 'question_type' => 'multiple_choice', 'options' => ['docker build', 'docker create', 'docker init', 'docker run'], 'correct_answer' => 'docker build'],
            ['question_text' => 'What is a Docker container?', 'question_type' => 'text', 'correct_answer' => 'A lightweight, isolated environment that runs applications'],
            ['question_text' => 'Which file defines the instructions to build a Docker image?', 'question_type' => 'text', 'correct_answer' => 'Dockerfile'],

            // TensorFlow
            ['question_text' => 'TensorFlow is primarily used for?', 'question_type' => 'multiple_choice', 'options' => ['Web development','Machine learning','Database management','Networking'], 'correct_answer' => 'Machine learning'],
            ['question_text' => 'Which type of neural network is commonly used in TensorFlow for image recognition?', 'question_type' => 'text', 'correct_answer' => 'Convolutional Neural Network (CNN)'],
            ['question_text' => 'Which method compiles a model in TensorFlow?', 'question_type' => 'multiple_choice', 'options' => ['model.run()','model.fit()','model.compile()','model.train()'], 'correct_answer' => 'model.compile()'],

            // PyTorch
            ['question_text' => 'PyTorch uses which type of computation graph?', 'question_type' => 'multiple_choice', 'options' => ['Static','Dynamic','Frozen','Compiled'], 'correct_answer' => 'Dynamic'],
            ['question_text' => 'Which PyTorch class is used to define a neural network?', 'question_type' => 'text', 'correct_answer' => 'torch.nn.Module'],
            ['question_text' => 'Which function converts a PyTorch tensor to a NumPy array?', 'question_type' => 'text', 'correct_answer' => 'tensor.numpy()'],

            // SQL
            ['question_text' => 'Which SQL command is used to remove a table?', 'question_type' => 'multiple_choice', 'options' => ['DELETE','DROP','REMOVE','TRUNCATE'], 'correct_answer' => 'DROP'],
            ['question_text' => 'What does the PRIMARY KEY constraint do in SQL?', 'question_type' => 'text', 'correct_answer' => 'Uniquely identifies each record in a table'],
            ['question_text' => 'Which SQL clause is used to filter records?', 'question_type' => 'multiple_choice', 'options' => ['WHERE','HAVING','FILTER','LIMIT'], 'correct_answer' => 'WHERE'],

            // MongoDB
            ['question_text' => 'MongoDB stores data in which format?', 'question_type' => 'multiple_choice', 'options' => ['JSON','XML','CSV','SQL'], 'correct_answer' => 'JSON'],
            ['question_text' => 'Which command is used to insert a document in MongoDB?', 'question_type' => 'text', 'correct_answer' => 'db.collection.insertOne()'],
            ['question_text' => 'Which command retrieves all documents in a MongoDB collection?', 'question_type' => 'text', 'correct_answer' => 'db.collection.find()'],

            // HTML
            ['question_text' => 'What does HTML stand for?', 'question_type' => 'text', 'correct_answer' => 'HyperText Markup Language'],
            ['question_text' => 'Who is making the Web standards?', 'question_type' => 'multiple_choice', 'options' => ['Mozilla','Google','Microsoft','World Wide Web Consortium'], 'correct_answer' => 'World Wide Web Consortium'],
            ['question_text' => 'Which attribute specifies an image source in HTML?', 'question_type' => 'text', 'correct_answer' => 'src'],

            // CSS
            ['question_text' => 'Which CSS property changes text color?', 'question_type' => 'multiple_choice', 'options' => ['font-color','text-color','color','foreground'], 'correct_answer' => 'color'],
            ['question_text' => 'Which CSS property is used for spacing inside an element?', 'question_type' => 'text', 'correct_answer' => 'padding'],
            ['question_text' => 'Which CSS property sets the background color of an element?', 'question_type' => 'text', 'correct_answer' => 'background-color'],

            // JavaScript
            ['question_text' => 'Which keyword declares a variable in JavaScript?', 'question_type' => 'multiple_choice', 'options' => ['var','let','const','All of the above'], 'correct_answer' => 'All of the above'],
            ['question_text' => 'What is a closure in JavaScript?', 'question_type' => 'text', 'correct_answer' => 'A function with access to its outer scope variables'],
            ['question_text' => 'Which method adds an element to the end of an array?', 'question_type' => 'multiple_choice', 'options' => ['push()','pop()','shift()','unshift()'], 'correct_answer' => 'push()'],
        ];

        foreach ($questions as $q) {
            AssessmentQuestion::create([
                'question_text' => $q['question_text'],
                'question_type' => $q['question_type'],
                'options' => isset($q['options']) ? json_encode($q['options']) : null,
                'correct_answer' => $q['correct_answer'],
            ]);
        }
    }
}