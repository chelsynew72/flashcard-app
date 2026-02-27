<script setup lang="ts">
import { useRouter } from 'vue-router'
const router = useRouter()
</script>

<template>
  <div class="landing">
    <nav class="nav">
      <div class="nav-inner">
        <div class="logo">
          <span class="logo-icon">🃏</span>
          <span class="logo-text">FlashcardApp</span>
        </div>
        <div class="nav-links">
          <a href="#features">Features</a>
          <a href="#how-it-works">How It Works</a>
          <a href="#stats">Stats</a>
        </div>
        <div class="nav-actions">
          <button class="btn-ghost" @click="router.push('/login')">Login</button>
          <button class="btn-primary" @click="router.push('/register')">Sign Up</button>
        </div>
      </div>
    </nav>

    <section class="hero">
      <div class="hero-left">
        <div class="badge">
          <span class="badge-dot"></span>
          New: AI Deck Generation
        </div>
        <h1 class="hero-title">
          Master Any Subject with
          <span class="gradient-text">Spaced Repetition</span>
        </h1>
        <p class="hero-subtitle">
          Our intelligent algorithm adapts to your personal learning pace,
          helping you memorize faster and retain information significantly
          longer than traditional methods.
        </p>
        <div class="hero-actions">
          <button class="btn-primary btn-lg" @click="router.push('/register')">Start Learning Free</button>
          <button class="btn-ghost btn-lg">
            <span class="play-icon">▶</span>
            Watch Demo
          </button>
        </div>
      </div>
      <div class="hero-right">
        <div class="card-preview">
          <div class="card-preview-bar">
            <span class="dot red"></span>
            <span class="dot yellow"></span>
            <span class="dot green"></span>
          </div>
          <div class="card-preview-body">
            <p class="card-label">QUESTION</p>
            <h3 class="card-question">What is the "Forgetting Curve"?</h3>
          </div>
          <div class="card-preview-actions">
            <button class="rating-btn again">Again</button>
            <button class="rating-btn hard">Hard</button>
            <button class="rating-btn good">Good</button>
            <button class="rating-btn easy">Easy</button>
          </div>
        </div>
      </div>
    </section>

    <section class="stats-bar" id="stats">
      <div class="stat-item">
        <p class="stat-label">ACTIVE LEARNERS</p>
        <p class="stat-value">1M+</p>
      </div>
      <div class="stat-item">
        <p class="stat-label">CARDS CREATED</p>
        <p class="stat-value">500M+</p>
      </div>
      <div class="stat-item">
        <p class="stat-label">RETENTION RATE</p>
        <p class="stat-value">95%</p>
      </div>
      <div class="stat-item">
        <p class="stat-label">COUNTRIES</p>
        <p class="stat-value">120+</p>
      </div>
    </section>

    <section class="features" id="features">
      <div class="section-header">
        <h2>Powerful Features for <span class="gradient-text">Efficient Learning</span></h2>
        <p>Everything you need to crush your exams and master new skills with zero friction.</p>
      </div>
      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon">🧠</div>
          <h3>Spaced Repetition</h3>
          <p>Science-based algorithms optimize your review schedule automatically.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">💡</div>
          <h3>Active Recall</h3>
          <p>Engage your brain actively for deeper and more durable memory encoding.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">🔄</div>
          <h3>Cloud Sync</h3>
          <p>Study on your phone, tablet, or desktop seamlessly without losing progress.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">📡</div>
          <h3>Offline Mode</h3>
          <p>Download your decks and study anywhere, anytime even without internet.</p>
        </div>
      </div>
    </section>

    <section class="how-it-works" id="how-it-works">
      <div class="section-header center">
        <h2>How It Works</h2>
        <p>Mastering a subject has never been more straightforward.</p>
      </div>
      <div class="steps-grid">
        <div class="step">
          <div class="step-number">1</div>
          <h3>Create Your Decks</h3>
          <p>Import notes or create cards manually with our rich markdown editor. Add images, audio, and more.</p>
        </div>
        <div class="step-connector"></div>
        <div class="step">
          <div class="step-number">2</div>
          <h3>Start Reviewing</h3>
          <p>Our algorithm selects the cards you're most likely to forget, focusing your energy where it matters.</p>
        </div>
        <div class="step-connector"></div>
        <div class="step">
          <div class="step-number">3</div>
          <h3>Track Growth</h3>
          <p>Visualize your mastery level with heatmaps and detailed statistics for every subject.</p>
        </div>
      </div>
    </section>

    <section class="cta">
      <h2>Ready to stop forgetting?</h2>
      <p>Join over a million students, medical professionals, and lifelong learners today.</p>
      <button class="btn-primary btn-lg" @click="router.push('/register')">Create Your Free Account</button>
      <p class="cta-note">No credit card required. Free forever version available.</p>
    </section>

    <footer class="footer">
      <div class="footer-inner">
        <div class="logo">
          <span class="logo-icon">🃏</span>
          <span class="logo-text">FlashcardApp</span>
        </div>
        <div class="footer-links">
          <a href="#">Privacy Policy</a>
          <a href="#">Terms of Service</a>
          <a href="#">Contact Us</a>
        </div>
        <p class="footer-copy">© 2026 FlashcardApp. All rights reserved.</p>
      </div>
    </footer>
  </div>
</template>

<style scoped>
.landing {
  background: #0a1f1c;
  color: #e8f5f0;
  font-family: 'DM Sans', sans-serif;
}

.gradient-text {
  background: linear-gradient(135deg, #00e5b4, #00c896);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* NAV */
.nav {
  position: sticky;
  top: 0;
  z-index: 100;
  background: rgba(10, 31, 28, 0.9);
  backdrop-filter: blur(16px);
  border-bottom: 1px solid rgba(0, 229, 180, 0.08);
}
.nav-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 16px 40px;
  display: flex;
  align-items: center;
  gap: 40px;
}
.logo { display: flex; align-items: center; gap: 10px; margin-right: auto; }
.logo-icon { font-size: 22px; }
.logo-text { font-size: 18px; font-weight: 700; color: #e8f5f0; }
.nav-links { display: flex; gap: 32px; }
.nav-links a { color: #8ab8a8; text-decoration: none; font-size: 15px; font-weight: 500; transition: color 0.2s; }
.nav-links a:hover { color: #e8f5f0; }
.nav-actions { display: flex; gap: 12px; align-items: center; }

/* BUTTONS */
.btn-primary {
  background: #00e5b4;
  color: #0a1f1c;
  border: none;
  padding: 10px 22px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
}
.btn-primary:hover {
  background: #00c896;
  transform: translateY(-1px);
  box-shadow: 0 8px 24px rgba(0, 229, 180, 0.3);
}
.btn-ghost {
  background: transparent;
  color: #8ab8a8;
  border: 1px solid rgba(0, 229, 180, 0.2);
  padding: 10px 22px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  gap: 8px;
}
.btn-ghost:hover { border-color: rgba(0, 229, 180, 0.5); color: #e8f5f0; }
.btn-lg { padding: 14px 32px; font-size: 16px; border-radius: 10px; }
.play-icon {
  font-size: 10px;
  background: rgba(0, 229, 180, 0.15);
  width: 26px; height: 26px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
}

/* HERO */
.hero {
  max-width: 1200px;
  margin: 0 auto;
  padding: 100px 40px;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 80px;
  align-items: center;
}
.hero-left { display: flex; flex-direction: column; gap: 28px; }
.badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(0, 229, 180, 0.08);
  border: 1px solid rgba(0, 229, 180, 0.2);
  padding: 6px 14px;
  border-radius: 999px;
  font-size: 13px;
  color: #00e5b4;
  font-weight: 600;
  width: fit-content;
}
.badge-dot {
  width: 7px; height: 7px;
  background: #00e5b4;
  border-radius: 50%;
  animation: pulse 2s infinite;
}
@keyframes pulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.5; transform: scale(0.8); }
}
.hero-title { font-size: 52px; font-weight: 800; line-height: 1.1; letter-spacing: -1.5px; color: #ffffff; }
.hero-subtitle { font-size: 16px; line-height: 1.7; color: #8ab8a8; max-width: 480px; }
.hero-actions { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }

/* CARD PREVIEW */
.card-preview {
  background: #0f2b26;
  border: 1px solid rgba(0, 229, 180, 0.12);
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 32px 64px rgba(0, 0, 0, 0.4);
}
.card-preview-bar {
  background: #0d2420;
  padding: 12px 16px;
  display: flex;
  gap: 6px;
  align-items: center;
  border-bottom: 1px solid rgba(0, 229, 180, 0.08);
}
.dot { width: 10px; height: 10px; border-radius: 50%; }
.dot.red { background: #ff5f57; }
.dot.yellow { background: #febc2e; }
.dot.green { background: #28c840; }
.card-preview-body { padding: 60px 40px; text-align: center; }
.card-label { font-size: 11px; letter-spacing: 2px; color: #00e5b4; font-weight: 700; margin-bottom: 20px; }
.card-question { font-size: 22px; font-weight: 700; color: #ffffff; line-height: 1.4; }
.card-preview-actions {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 8px;
  padding: 16px;
  border-top: 1px solid rgba(0, 229, 180, 0.08);
}
.rating-btn { padding: 10px; border-radius: 8px; border: none; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.2s; }
.rating-btn.again { background: rgba(255,100,100,0.15); color: #ff6464; }
.rating-btn.hard { background: rgba(255,165,0,0.15); color: #ffa500; }
.rating-btn.good { background: rgba(0,229,180,0.15); color: #00e5b4; }
.rating-btn.easy { background: rgba(100,149,237,0.15); color: #6495ed; }
.rating-btn:hover { transform: translateY(-2px); filter: brightness(1.2); }

/* STATS BAR */
.stats-bar {
  background: #0d2420;
  border-top: 1px solid rgba(0, 229, 180, 0.08);
  border-bottom: 1px solid rgba(0, 229, 180, 0.08);
  display: grid;
  grid-template-columns: repeat(4, 1fr);
}
.stat-item { padding: 40px; text-align: center; border-right: 1px solid rgba(0, 229, 180, 0.08); }
.stat-item:last-child { border-right: none; }
.stat-label { font-size: 11px; letter-spacing: 2px; color: #8ab8a8; font-weight: 600; margin-bottom: 12px; }
.stat-value { font-size: 36px; font-weight: 800; color: #ffffff; letter-spacing: -1px; }

/* FEATURES */
.features { max-width: 1200px; margin: 0 auto; padding: 100px 40px; }
.section-header { margin-bottom: 60px; }
.section-header.center { text-align: center; }
.section-header h2 { font-size: 40px; font-weight: 800; letter-spacing: -1px; color: #ffffff; margin-bottom: 16px; }
.section-header p { font-size: 16px; color: #8ab8a8; line-height: 1.6; }
.features-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; }
.feature-card {
  background: #0f2b26;
  border: 1px solid rgba(0, 229, 180, 0.1);
  border-radius: 16px;
  padding: 32px 28px;
  transition: all 0.3s ease;
}
.feature-card:hover { border-color: rgba(0,229,180,0.3); transform: translateY(-4px); box-shadow: 0 16px 40px rgba(0,0,0,0.3); }
.feature-icon {
  font-size: 28px;
  width: 52px; height: 52px;
  background: rgba(0, 229, 180, 0.1);
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  margin-bottom: 20px;
}
.feature-card h3 { font-size: 17px; font-weight: 700; color: #ffffff; margin-bottom: 10px; }
.feature-card p { font-size: 14px; color: #8ab8a8; line-height: 1.6; }

/* HOW IT WORKS */
.how-it-works {
  background: #0d2420;
  border-top: 1px solid rgba(0, 229, 180, 0.08);
  border-bottom: 1px solid rgba(0, 229, 180, 0.08);
  padding: 100px 40px;
}
.steps-grid {
  max-width: 1000px;
  margin: 0 auto;
  display: flex;
  align-items: flex-start;
}
.step { flex: 1; text-align: center; padding: 0 24px; }
.step-connector {
  width: 80px;
  height: 2px;
  background: linear-gradient(90deg, rgba(0,229,180,0.3), rgba(0,229,180,0.1));
  margin-top: 28px;
  flex-shrink: 0;
}
.step-number {
  width: 56px; height: 56px;
  background: linear-gradient(135deg, #00e5b4, #00c896);
  color: #0a1f1c;
  border-radius: 50%;
  font-size: 22px; font-weight: 800;
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 24px;
}
.step h3 { font-size: 18px; font-weight: 700; color: #ffffff; margin-bottom: 12px; }
.step p { font-size: 14px; color: #8ab8a8; line-height: 1.6; }

/* CTA */
.cta {
  text-align: center;
  padding: 120px 40px;
  background: linear-gradient(180deg, #0a1f1c 0%, #0d2a24 100%);
}
.cta h2 { font-size: 52px; font-weight: 800; color: #ffffff; letter-spacing: -1.5px; margin-bottom: 20px; }
.cta p { font-size: 16px; color: #8ab8a8; margin-bottom: 40px; }
.cta-note { font-size: 13px !important; color: #5a8a78 !important; margin-top: 16px !important; margin-bottom: 0 !important; }

/* FOOTER */
.footer { background: #0d2420; border-top: 1px solid rgba(0, 229, 180, 0.08); padding: 32px 40px; }
.footer-inner { max-width: 1200px; margin: 0 auto; display: flex; align-items: center; gap: 32px; }
.footer-links { display: flex; gap: 24px; margin-left: auto; }
.footer-links a { font-size: 13px; color: #8ab8a8; text-decoration: none; transition: color 0.2s; }
.footer-links a:hover { color: #00e5b4; }
.footer-copy { font-size: 13px; color: #5a8a78; }
</style>
