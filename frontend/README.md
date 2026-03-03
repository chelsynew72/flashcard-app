# Flashcard App - Frontend SPA 🚀

The interactive frontend for the Flashcard App, built with **Vue 3** and **Vite**. It provides a rich user experience for managing decks, generating AI-based content, and conducting study sessions.

## 1. Problem Statement 💡
Static study materials can be boring and inefficient. This frontend is designed to provide an engaging, responsive, and intuitive interface that simplifies the process of creating and reviewing flashcards.

## 2. Technical Architecture 🏗️
- **Framework:** Vue 3 (Composition API)
- **State Management:** Pinia (Predictable and reactive data flow)
- **Styling:** Tailwind CSS (Modern, utility-first UI)
- **Tooling:** Vite, TypeScript, ESLint, Prettier
- **Navigation:** Vue Router (SPA-based navigation)

## 3. Features ✨
- **AI Card Generator UI:** Interactive topic-based generation with preview and selective save capabilities.
- **Dynamic Study Mode:** Card-based review system with real-time feedback and rating.
- **Deck Management:** Drag-and-drop-style interfaces for creating and organizing decks.
- **Community Portal:** Discovery and cloning of public educational resources.
- **Responsive Dashboard:** Personalized study metrics and deck statistics visualization.

## 4. Challenges Faced 🧠
- **Frontend Challenge:** Implementing the **AI generation workflow** where users can preview generated cards and selectively add them to new or existing decks required intricate **Pinia state management** and complex UI conditional rendering.
- **UI Consistency:** Maintaining a consistent, modern design system while using Tailwind CSS required creating a set of reusable UI components and a strict color palette.

## 5. What I Learned 🎓
- **Technical Lesson:** Advanced usage of **Vue 3 Composables** for sharing logic between different parts of the application (e.g., authentication status and study session timers).
- **Workflow Lesson:** Implementing **TypeScript** across the frontend significantly reduced runtime errors and improved code documentation.

## 6. Installation ⚙️
1. `npm install`
2. `npm run dev`
