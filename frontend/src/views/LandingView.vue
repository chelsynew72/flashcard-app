<script setup lang="ts">
import { useRouter } from 'vue-router'

const router = useRouter()

const handleGetStarted = () => {
  router.push({ name: 'signup' })
}

const handleLogin = () => {
  router.push({ name: 'login' })
}

const features = [
  {
    icon: '⏱️',
    title: 'Spaced Repetition',
    description: 'Algorithmically-timed reviews ensure you retain information longer with less effort using scientifically proven methods.',
  },
  {
    icon: '🎨',
    title: 'Beautiful Design',
    description: 'A distraction-free interface that helps you master a state of flow and stay focused on your learning goals.',
  },
  {
    icon: '📊',
    title: 'Detailed Stats',
    description: 'Visualize your progress with insightful charts and memory curves to identify your learning patterns.',
  },
]

const steps = [
  {
    number: '1',
    title: 'Create',
    description: 'Import your notes, use our AI to generate cards, or build your custom deck manually in seconds.',
  },
  {
    number: '2',
    title: 'Study',
    description: 'Review cards using smart intervals. Our app helps you exactly which cards to study each day.',
  },
  {
    number: '3',
    title: 'Track',
    description: 'Watch your mastery grow. Monitor your daily streak and performance data to stay motivated.',
  },
]
</script>

<template>
  <div class="landing-view">
    <header class="landing-header">
      <div class="header-content">
        <div class="logo">🎴</div>
        <div class="branding">
          <h1>FlashcardApp</h1>
        </div>
        <div class="header-actions">
          <button class="btn-login" @click="handleLogin">Login</button>
          <button class="btn-signup" @click="handleGetStarted">Sign Up</button>
        </div>
      </div>
    </header>

    <main class="landing-content">
      <section class="hero">
        <h2 class="hero-title">Master any subject, one card at a time</h2>
        <p class="hero-subtitle">The smart, simple flashcard app designed for high-performance students and lifelong learners.</p>
        <button class="btn-primary" @click="handleGetStarted">
          Get Started Free
          <span class="arrow">→</span>
        </button>
      </section>

      <section class="demo-section">
        <div class="demo-card">
          <h3>PHYSICS 1001</h3>
          <p class="demo-question">What is the law of universal gravitation?</p>
          <div class="demo-answer">
            <p>F = G(m₁m₂/r²) - The gravitational force between two bodies is proportional to their masses and inversely proportional to the square of distance.</p>
          </div>
        </div>
      </section>

      <section class="features">
        <h3 class="section-title">Study Smarter, Not Harder</h3>
        <div class="features-grid">
          <div v-for="(feature, index) in features" :key="index" class="feature-card">
            <div class="feature-icon">{{ feature.icon }}</div>
            <h4>{{ feature.title }}</h4>
            <p>{{ feature.description }}</p>
          </div>
        </div>
      </section>

      <section class="how-it-works">
        <h3 class="section-title">How It Works</h3>
        <div class="steps-grid">
          <div v-for="step in steps" :key="step.number" class="step-card">
            <div class="step-number">{{ step.number }}</div>
            <h4>{{ step.title }}</h4>
            <p>{{ step.description }}</p>
          </div>
        </div>
      </section>

      <section class="cta-section">
        <h3>Ready to transform your learning?</h3>
        <button class="btn-primary-large" @click="handleGetStarted">Get Started Free</button>
      </section>
    </main>

    <footer class="landing-footer">
      <div class="footer-content">
        <p>&copy; 2024 FlashcardApp. All rights reserved.</p>
        <div class="footer-links">
          <a href="#privacy">Privacy</a>
          <a href="#terms">Terms</a>
          <a href="#pricing">Pricing</a>
          <a href="#contact">Contact</a>
        </div>
      </div>
    </footer>
  </div>
</template>

<style scoped>
.landing-view {
  background: linear-gradient(135deg, #0a1628 0%, #0f1f33 100%);
  color: #ffffff;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell,
    sans-serif;
}

.landing-header {
  padding: 16px;
  border-bottom: 1px solid rgba(0, 255, 213, 0.1);
  position: sticky;
  top: 0;
  background: rgba(10, 22, 40, 0.95);
  backdrop-filter: blur(10px);
  z-index: 50;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
  max-width: 1200px;
  margin: 0 auto;
}

.logo {
  font-size: 24px;
}

.branding {
  flex: 1;
}

.branding h1 {
  font-size: 20px;
  font-weight: 700;
  margin: 0;
  letter-spacing: -0.5px;
}

.header-actions {
  display: flex;
  gap: 12px;
}

.btn-login {
  padding: 8px 16px;
  background: none;
  border: 1px solid rgba(0, 255, 213, 0.3);
  color: #00ffd5;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-login:hover {
  border-color: #00ffd5;
  background: rgba(0, 255, 213, 0.05);
}

.btn-signup {
  padding: 8px 16px;
  background: linear-gradient(135deg, #00ffd5 0%, #00d9b8 100%);
  border: none;
  color: #0a1628;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.btn-signup:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(0, 255, 213, 0.3);
}

.landing-content {
  flex: 1;
  padding: 24px 16px;
  max-width: 1200px;
  margin: 0 auto;
  width: 100%;
}

.hero {
  text-align: center;
  margin-bottom: 48px;
}

.hero-title {
  font-size: 32px;
  font-weight: 700;
  margin-bottom: 16px;
  letter-spacing: -0.5px;
  line-height: 1.2;
}

.hero-subtitle {
  font-size: 16px;
  color: #8a9db5;
  margin-bottom: 24px;
  max-width: 500px;
  margin-left: auto;
  margin-right: auto;
  line-height: 1.6;
}

.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: linear-gradient(135deg, #00ffd5 0%, #00d9b8 100%);
  color: #0a1628;
  border: none;
  padding: 14px 28px;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.btn-primary:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 24px rgba(0, 255, 213, 0.3);
}

.btn-primary:active {
  transform: translateY(-1px);
}

.arrow {
  font-size: 18px;
}

.demo-section {
  margin-bottom: 48px;
}

.demo-card {
  background: linear-gradient(135deg, rgba(0, 128, 128, 0.15) 0%, rgba(0, 100, 100, 0.08) 100%);
  border: 1px solid rgba(0, 255, 213, 0.2);
  border-radius: 16px;
  padding: 24px;
  margin-bottom: 24px;
}

.demo-card h3 {
  font-size: 14px;
  color: #8a9db5;
  margin: 0 0 16px 0;
  letter-spacing: 1px;
  font-weight: 600;
}

.demo-question {
  font-size: 18px;
  font-weight: 600;
  margin: 0 0 12px 0;
  color: #ffffff;
}

.demo-answer {
  background: rgba(0, 255, 213, 0.05);
  border-left: 3px solid #00ffd5;
  padding: 16px;
  border-radius: 8px;
}

.demo-answer p {
  margin: 0;
  font-size: 14px;
  line-height: 1.6;
  color: #a0b0c0;
}

.features {
  margin-bottom: 48px;
}

.section-title {
  font-size: 28px;
  font-weight: 700;
  text-align: center;
  margin-bottom: 32px;
}

.features-grid {
  display: grid;
  gap: 20px;
  grid-template-columns: 1fr;
}

.feature-card {
  background: linear-gradient(135deg, rgba(0, 128, 128, 0.1) 0%, rgba(0, 100, 100, 0.05) 100%);
  border: 1px solid rgba(0, 255, 213, 0.15);
  border-radius: 12px;
  padding: 24px;
  text-align: center;
}

.feature-icon {
  font-size: 40px;
  margin-bottom: 12px;
}

.feature-card h4 {
  font-size: 18px;
  margin: 0 0 8px 0;
}

.feature-card p {
  margin: 0;
  font-size: 14px;
  color: #8a9db5;
  line-height: 1.6;
}

.how-it-works {
  margin-bottom: 48px;
}

.steps-grid {
  display: grid;
  gap: 20px;
  grid-template-columns: 1fr;
}

.step-card {
  background: linear-gradient(135deg, rgba(0, 128, 128, 0.1) 0%, rgba(0, 100, 100, 0.05) 100%);
  border: 1px solid rgba(0, 255, 213, 0.15);
  border-radius: 12px;
  padding: 24px;
}

.step-number {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, #00ffd5 0%, #00d9b8 100%);
  color: #0a1628;
  border-radius: 50%;
  font-size: 24px;
  font-weight: 700;
  margin-bottom: 12px;
}

.step-card h4 {
  font-size: 20px;
  margin: 0 0 8px 0;
}

.step-card p {
  margin: 0;
  font-size: 14px;
  color: #8a9db5;
  line-height: 1.6;
}

.cta-section {
  text-align: center;
  padding: 48px 24px;
  background: linear-gradient(135deg, rgba(0, 255, 213, 0.1) 0%, rgba(0, 200, 200, 0.05) 100%);
  border: 1px solid rgba(0, 255, 213, 0.2);
  border-radius: 16px;
  margin-bottom: 48px;
}

.cta-section h3 {
  font-size: 24px;
  margin-bottom: 24px;
}

.btn-primary-large {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: linear-gradient(135deg, #00ffd5 0%, #00d9b8 100%);
  color: #0a1628;
  border: none;
  padding: 16px 32px;
  border-radius: 12px;
  font-size: 18px;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.btn-primary-large:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 24px rgba(0, 255, 213, 0.3);
}

.landing-footer {
  padding: 24px 16px;
  border-top: 1px solid rgba(0, 255, 213, 0.1);
  background: rgba(10, 22, 40, 0.5);
}

.footer-content {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 24px;
}

.footer-content p {
  margin: 0;
  font-size: 12px;
  color: #5a6f8a;
}

.footer-links {
  display: flex;
  gap: 24px;
}

.footer-links a {
  color: #8a9db5;
  text-decoration: none;
  font-size: 12px;
  transition: color 0.2s ease;
}

.footer-links a:hover {
  color: #00ffd5;
}

@media (min-width: 768px) {
  .features-grid,
  .steps-grid {
    grid-template-columns: repeat(3, 1fr);
  }

  .hero-title {
    font-size: 48px;
  }

  .hero-subtitle {
    font-size: 18px;
  }
}
</style>
