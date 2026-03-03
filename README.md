# Flashcard App 

**Flashcard App** is a modern, AI-powered learning platform designed to help students and lifelong learners master any subject through intelligent card generation and spaced repetition.

## 1. Problem Statement 
Traditional flashcard applications often require hours of manual data entry, creating a barrier to consistent learning. Many students find it difficult to identify the most important concepts to study from their materials. Flashcard App solves this by leveraging **AI-driven generation** to instantly create high-quality study cards based on a topic or difficulty level, combined with a **Spaced Repetition System (SRS)** to maximize long-term retention.

---

## 2. Technical Architecture 
The application follows a modern decoupled architecture:

- **Frontend (SPA):** Built with **Vue 3**, **Vite**, and **TypeScript**. It utilizes a component-based architecture with **Pinia** for centralized state management and **Tailwind CSS** for a responsive, modern UI.
- **Backend (RESTful API):** Developed using **Laravel 12 (PHP 8.2)**. It handles authentication via **Sanctum**, manages relational data (MySQL/PostgreSQL), and orchestrates AI services for card generation.
- **Database:** A relational schema designed to manage complex relationships between users, decks, cards, study sessions, and tags.
- **API Communication:** Uses **Axios** with interceptors for seamless token-based authentication and standardized error handling.

---

## 3. Tech Stack 🛠️

- **Frontend:** Vue.js 3, TypeScript, Pinia, Vue Router, Tailwind CSS, Vite.
- **Backend:** Laravel 12, PHP 8.2, Sanctum (Auth), Socialite (Google OAuth).
- **Database:** MySQL / PostgreSQL.
- **DevOps:** Docker, Nixpacks.
- **AI Integration:** Laravel-based AI Controller (LLM Integration).

---

## 4. Features (Beyond CRUD) 

- **AI Card Generation:** Instantly generate high-quality flashcards by providing a topic and difficulty level (Beginner, Intermediate, Advanced).
- **Intelligent Study Sessions:** A rating-based progress system that tracks card performance to determine review intervals.
- **Google OAuth Integration:** Seamless one-click authentication for a frictionless user experience.
- **Community Explorer:** Discover, preview, and clone public decks created by other users.
- **Responsive Dashboard:** Real-time data visualization of study progress, recent activity, and deck statistics.
- **Advanced Deck Management:** Features like deck cloning, tagging, and batch card importing.

---

## 5. Challenges Faced 

- **Frontend Challenge:** Implementing a seamless AI generation workflow required complex state management in Vue 3 using Pinia, specifically handling the preview, selective card selection, and batch saving to a deck.
- **Backend Challenge:** Designing a flexible study session API that tracks individual card ratings and efficiently determines the next card to show based on past performance while maintaining low latency.
- **Debugging Experience:** Resolved an issue where Sanctum CSRF cookies were not being sent correctly in development by precisely configuring the CORS policy and Axios `withCredentials` settings.

---

## 6. What I Learned 

- **Technical Lesson:** Deepened my understanding of Vue 3's Composition API and how to effectively manage asynchronous state for long-running AI operations.

- **Code Organization Lesson:** Adopted a cleaner "Action/Service" pattern in Laravel to keep controllers thin and logic reusable.

---

## 7. Future Improvements 

- **Advanced SRS Algorithm:** Implementation of the SM-2 or FSRS algorithm for even more precise study scheduling.
- **Mobile Application:** Developing a mobile-first version using  React Native for learning on the go.
- **OCR Integration:** Allow users to upload photos of textbooks or handwritten notes to generate flashcards automatically.

---

## 8. Installation Instructions ⚙️

### Prerequisites
- PHP 8.2+
- Node.js 20+
- Composer
- MySQL/PostgreSQL

### Backend Setup
1. `cd backend`
2. `composer install`
3. `cp .env.example .env` (Configure your database and AI API keys)
4. `php artisan key:generate`
5. `php artisan migrate`
6. `php artisan serve`

### Frontend Setup
1. `cd frontend`
2. `npm install`
3. `npm run dev`

### Root Development
Alternatively, run both from the root:
`npm install && npm run dev`

---

## Live Demo 
[Check out the Live Demo](https://flashcard-app-five-gamma.vercel.app/) (Placeholder)

---

## Contact Information 
**Author:** Ameah tem chelsy
 

