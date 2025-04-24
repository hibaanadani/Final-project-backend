CREATE TABLE users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(255) UNIQUE NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL
 );
 
CREATE TABLE quizzes (
  quiz_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL
 );
 
INSERT INTO quizzes (quiz_id, name) VALUES (1, 'Geography & Travel');
INSERT INTO quizzes (quiz_id, name) VALUES (2, 'Movies & TV Shows');
INSERT INTO quizzes (quiz_id, name) VALUES (3, 'Music & Pop Culture');
INSERT INTO quizzes (quiz_id, name) VALUES (4, 'Books & Literature');
INSERT INTO quizzes (quiz_id, name) VALUES (5, 'Animals & Nature');
INSERT INTO quizzes (quiz_id, name) VALUES (6, 'History & Mythology');
INSERT INTO quizzes (quiz_id, name) VALUES (7, 'Sports & Games');
INSERT INTO quizzes (quiz_id, name) VALUES (8, 'Food & Drink');

CREATE TABLE questions (
  question_id INT AUTO_INCREMENT PRIMARY KEY,
  quiz_id INT NOT NULL,
  question_text TEXT NOT NULL,
  options TEXT NOT NULL,
  correct_answer VARCHAR(255) NOT NULL,
  FOREIGN KEY (quiz_id) REFERENCES quizzes(quiz_id)
 );
 
INSERT INTO questions (quiz_id, question_text, options, correct_answer) VALUES
(1, "Which country has the most natural lakes?", '["Canada", "Russia", "USA"]', "Canada"),
(1, "Which continent has the most countries?", '["Europe", "Africa", "Asia"]', "Africa"),
(1, "What is the longest river in the world?", '["Amazon River", "Nile River", "Yangtze River"]', "Nile River");

INSERT INTO questions (quiz_id, question_text, options, correct_answer) VALUES
(2, "Which movie features the quote 'May the Force be with you'?", '["Star Wars", "The Matrix", "Interstellar"]', "Star Wars"),
(2, "What is the name of Ross Geller’s second wife?", '["Janice", "Rachel", "Emily"]', "Emily"),
(2, "What is the highest-grossing movie of all time?", '["Avengers: Endgame", "Avatar", "Titanic"]', "Avatar");

INSERT INTO questions (quiz_id, question_text, options, correct_answer) VALUES
(3, "Which artist is known as the 'King of Pop'?", '["Michael Jackson", "Elvis Presley", "Prince"]', "Michael Jackson"),
(3, "What was the first music video ever played on MTV?", '["Thriller", "Video Killed the Radio Star", "Bohemian Rhapsody"]', "Video Killed the Radio Star"),
(3, "Which band recorded the song 'Bohemian Rhapsody'?", '["The Beatles", "Queen", "The Rolling Stones"]', "Queen");

INSERT INTO questions (quiz_id, question_text, options, correct_answer) VALUES
(4, "Who wrote 'Pride and Prejudice'?", '["Charlotte Brontë", "Jane Austen", "Emily Dickinson"]', "Jane Austen"),
(4, "What is the name of the witcher Geralt of Rivia's trusted horse?", '["Roach", "Shadowfax", "Storm"]', "Roach"),
(4, "Who is the author of the Harry Potter series?", '["J.K. Rowling", "J.R.R. Tolkien", "George R.R. Martin"]', "J.K. Rowling");

INSERT INTO questions (quiz_id, question_text, options, correct_answer) VALUES
(5, "Which is the largest land animal?", '["Elephant", "Hippopotamus", "Giraffe"]', "Elephant"),
(5, "Which bird is known for its ability to mimic human speech?", '["Parrot", "Crow", "Eagle"]', "Parrot"),
(5, "Which mammal is capable of true flight?", '["Squirrel", "Bat", "Flying Fox"]', "Bat");

INSERT INTO questions (quiz_id, question_text, options, correct_answer) VALUES
(6, "Who was the first President of the United States?", '["George Washington", "Thomas Jefferson", "Abraham Lincoln"]', "George Washington"),
(6, "In Greek mythology, who is the god of the underworld?", '["Zeus", "Hades", "Poseidon"]', "Hades"),
(6, "The Great Fire of London happened in which year?", '["1666", "1776", "1812"]', "1666");

INSERT INTO questions (quiz_id, question_text, options, correct_answer) VALUES
(7, "Which country won the first-ever FIFA World Cup?", '["Brazil", "Uruguay", "Germany"]', "Uruguay"),
(7, "How many players are on a standard soccer team?", '["11", "9", "15"]', "11"),
(7, "Which game features a king, queen, rook, bishop, knight, and pawn?", '["Checkers", "Go", "Chess"]', "Chess");

INSERT INTO questions (quiz_id, question_text, options, correct_answer) VALUES
(8, "What is the main ingredient in guacamole?", '["Avocado", "Tomato", "Lettuce"]', "Avocado"),
(8, "Which country is known for sushi?", '["Japan", "China", "Thailand"]', "Japan"),
(8, "Which type of pasta is shaped like a small rice grain?", '["Penne", "Orzo", "Spaghetti"]', "Orzo");


CREATE TABLE scores (
 score_id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 quiz_id INT NOT NULL, 
 score INT NOT NULL,
 FOREIGN KEY (user_id) REFERENCES users(user_id),
 FOREIGN KEY (quiz_id) REFERENCES quizzes(quiz_id)
);