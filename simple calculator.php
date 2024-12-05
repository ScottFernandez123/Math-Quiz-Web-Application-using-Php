<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Math Quiz Application</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
        }
        #app {
            border: 2px solid black;
            padding: 20px;
            background: white;
            max-width: 600px;
            width: 100%;
        }
        button {
            margin: 5px;
            padding: 10px;
            border: 2px solid black;
            cursor: pointer;
        }
        button.correct {
            background-color: green;
            color: white;
        }
        button.incorrect {
            background-color: red;
            color: white;
        }
        #settings {
            margin-bottom: 20px;
        }
        .hidden {
            display: none;
        }
        .question, .answers, #remarks, #score {
            margin-top: 20px;
        }
        #toggle-settings, #start-quiz {
            display: inline-block;
            margin-left: 180px;
            margin-right: -170px;
        }
    </style>
</head>
<body>
    <div id="app">
        <div id="settings">
            <h3 style="text-align: center;">Settings</h3>
            <div id="operator-section">
                <label>Operator:</label>
                <select id="operator">
                    <option value="+">Addition</option>
                    <option value="-">Subtraction</option>
                    <option value="*">Multiplication</option>
                    <option value="/">Division</option>
                </select>
            </div>

            <div id="items-section">
                <label>Number of items:</label>
                <input type="number" id="num-items" min="1" value="5">
                <label>Max difference:</label>
                <input type="number" id="max-difference" min="1" value="10">
            </div>

            <div id="level-section">
                <label>Level:</label>
                <select id="level">
                    <option value="1-10">Level 1 (1-10)</option>
                    <option value="11-100">Level 2 (11-100)</option>
                    <option value="custom">Custom Level</option>
                </select>
                <div id="custom-level" class="hidden">
                    <label>Min:</label>
                    <input type="number" id="custom-min" min="1" value="1">
                    <label>Max:</label>
                    <input type="number" id="custom-max" min="1" value="10">
                </div>
            </div>
        </div>

        <button id="toggle-settings">Toggle Settings</button>
        <button id="start-quiz">Start Quiz</button>
        <div id="quiz" class="hidden">
            <div class="question"></div>
            <div class="answers"></div>
            <div id="remarks"></div>
            <div id="score">Score: 0 Correct, 0 Incorrect</div>
            <button id="end-quiz">End</button>
        </div>
    </div>

    <script>
        const settingsSection = document.getElementById('settings');
        const toggleSettingsBtn = document.getElementById('toggle-settings');
        const startQuizBtn = document.getElementById('start-quiz');
        const quizSection = document.getElementById('quiz');
        const questionDiv = document.querySelector('.question');
        const answersDiv = document.querySelector('.answers');
        const remarksDiv = document.getElementById('remarks');
        const scoreDiv = document.getElementById('score');
        const endQuizBtn = document.getElementById('end-quiz');
        const levelSelector = document.getElementById('level');
        const customLevelDiv = document.getElementById('custom-level');

        let correctAnswers = 0;
        let incorrectAnswers = 0;
        let totalQuestions = 0;
        let questionsLeft = 0;

        toggleSettingsBtn.addEventListener('click', () => {
            settingsSection.classList.toggle('hidden');
        });

        levelSelector.addEventListener('change', () => {
            if (levelSelector.value === 'custom') {
                customLevelDiv.classList.remove('hidden');
            } else {
                customLevelDiv.classList.add('hidden');
            }
        });

        startQuizBtn.addEventListener('click', () => {
            const operator = document.getElementById('operator').value;
            const numItems = parseInt(document.getElementById('num-items').value);
            const maxDifference = parseInt(document.getElementById('max-difference').value);

            let min, max;
            if (levelSelector.value === 'custom') {
                min = parseInt(document.getElementById('custom-min').value);
                max = parseInt(document.getElementById('custom-max').value);
            } else {
                [min, max] = levelSelector.value.split('-').map(Number);
            }

            totalQuestions = numItems;
            questionsLeft = numItems;
            correctAnswers = 0;
            incorrectAnswers = 0;

            quizSection.classList.remove('hidden');
            settingsSection.classList.add('hidden');
            generateQuestion(operator, min, max, maxDifference);
        });

        function generateQuestion(operator, min, max, maxDifference) {
            if (questionsLeft <= 0) {
                const grade = ((correctAnswers / totalQuestions) * 50 + 50).toFixed(2);
                alert(`Quiz Finished!\nCorrect: ${correctAnswers}, Incorrect: ${incorrectAnswers}\nGrade: ${grade}%`);
                resetQuiz();
                return;
            }

            const num1 = Math.floor(Math.random() * (max - min + 1)) + min;
            const num2 = Math.floor(Math.random() * (max - min + 1)) + min;
            const correctAnswer = Math.round(eval(`${num1} ${operator} ${num2}`));

            const answers = new Set([correctAnswer]);
            while (answers.size < 4) {
                const fakeAnswer = Math.round(Math.random() * (2 * maxDifference) - maxDifference + parseFloat(correctAnswer));
                answers.add(fakeAnswer);
            }

            const shuffledAnswers = Array.from(answers).sort(() => Math.random() - 0.5);

            questionDiv.textContent = `What is ${num1} ${operator} ${num2}?`;
            answersDiv.innerHTML = '';

            shuffledAnswers.forEach(answer => {
                const button = document.createElement('button');
                button.textContent = answer;
                button.addEventListener('click', () => {
                    if (answer == correctAnswer) {
                        button.classList.add('correct');
                        remarksDiv.textContent = 'Correct answer!';
                        correctAnswers++;
                    } else {
                        button.classList.add('incorrect');
                        remarksDiv.textContent = 'Wrong answer!';
                        incorrectAnswers++;
                    }
                    updateScore();
                    questionsLeft--;
                    setTimeout(() => {
                        generateQuestion(operator, min, max, maxDifference);
                    }, 1000);
                });
                answersDiv.appendChild(button);
            });
        }

        function updateScore() {
            scoreDiv.textContent = `Score: ${correctAnswers} Correct, ${incorrectAnswers} Incorrect`;
        }

        function resetQuiz() {
            quizSection.classList.add('hidden');
            settingsSection.classList.remove('hidden');
            remarksDiv.textContent = '';
            scoreDiv.textContent = 'Score: 0 Correct, 0 Incorrect';
        }

        endQuizBtn.addEventListener('click', resetQuiz);
    </script>
</body>
</html>
