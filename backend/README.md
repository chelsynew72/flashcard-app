# Flashcard App - Backend API 🧠

The backend for the Flashcard App, built with **Laravel 12**. This API manages users, decks, cards, study sessions, and provides AI-powered card generation.

## 1. Problem Statement 💡
The manual creation of high-quality flashcards is time-consuming and cognitively demanding. This backend aims to reduce that friction by providing automated tools for card generation and an intelligent study management system.

## 2. Technical Architecture 🏗️
- **Framework:** Laravel 12 (PHP 8.2+)
- **Authentication:** Laravel Sanctum (Token-based) & Socialite (Google OAuth)
- **Database:** Relational (MySQL/PostgreSQL) with Eloquent ORM
- **Services:** Dedicated `AiService` for LLM integration and `StudySessionService` for spaced repetition logic.

## 3. Features ✨
- **RESTful API:** Clean and versioned endpoints (`/api/v1/...`).
- **AI Card Generation:** Integration with AI models to generate structured JSON flashcards from any topic.
- **SRS Implementation:** Tracks study session performance and calculates review intervals.
- **Deck & Card Management:** Full CRUD for organizing study materials.
- **Public Deck Explorer:** Allows users to discover and clone shared educational content.

## 4. Challenges Faced 🧠
- **Backend Challenge:** Implementing the **Spaced Repetition logic** where each card rating influences the probability of it appearing in future sessions required complex query optimization and database indexing.
- **Security:** Implementing **Sanctum-based authentication** across both standard login and Google OAuth while maintaining a secure CSRF policy for the frontend SPA.

## 5. What I Learned 🎓
- **Technical Lesson:** Mastered the use of **Laravel Policies** for fine-grained authorization (e.g., ensuring users can only edit their own decks).
- **Workflow Lesson:** Learned how to effectively mock external AI API responses during testing to ensure a reliable CI/CD pipeline.

## 6. Installation ⚙️
1. `composer install`
2. `cp .env.example .env` (Configure DB & AI Keys)
3. `php artisan key:generate`
4. `php artisan migrate`
5. `php artisan serve`
