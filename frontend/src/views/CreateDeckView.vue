<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const title = ref('')
const description = ref('')
const tags = ref(['Science', 'Study2024', 'Exams'])
const newTag = ref('')
const isPublic = ref(true)

const handleBack = () => {
  router.back()
}

const addTag = () => {
  if (newTag.value.trim() && !tags.value.includes(newTag.value.trim())) {
    tags.value.push(newTag.value.trim())
    newTag.value = ''
  }
}

const removeTag = (tagToRemove: string) => {
  tags.value = tags.value.filter((tag) => tag !== tagToRemove)
}

const handleSave = () => {
  // Logic to save the deck would go here
  console.log('Saving deck:', {
    title: title.value,
    description: description.value,
    tags: tags.value,
    isPublic: isPublic.value,
  })
  router.push({ name: 'library' })
}
</script>

<template>
  <div class="create-deck-view">
    <div class="container">
      <header class="view-header">
        <button class="back-button" @click="handleBack">
          <span class="icon">←</span>
        </button>
        <h1 class="header-title">Create New Deck</h1>
        <button class="cancel-button" @click="handleBack">Cancel</button>
      </header>

      <main class="form-content">
        <div class="form-group">
          <label for="deck-title">Deck Title</label>
          <input
            id="deck-title"
            v-model="title"
            type="text"
            placeholder="e.g., Organic Chemistry"
            class="form-input"
          />
        </div>

        <div class="form-group">
          <label for="deck-description">Description</label>
          <textarea
            id="deck-description"
            v-model="description"
            placeholder="What is this deck about?"
            class="form-textarea"
          ></textarea>
        </div>

        <div class="form-group">
          <label>Tags</label>
          <div class="tags-container">
            <span v-for="tag in tags" :key="tag" class="tag">
              {{ tag }}
              <button class="remove-tag" @click="removeTag(tag)">×</button>
            </span>
          </div>
          <div class="add-tag-input-wrapper">
            <span class="tag-icon">🏷️</span>
            <input
              v-model="newTag"
              type="text"
              placeholder="Add a tag..."
              class="tag-input"
              @keyup.enter="addTag"
            />
          </div>
        </div>

        <div class="toggle-group">
          <div class="toggle-info">
            <span class="toggle-label">Make this deck public</span>
            <span class="toggle-hint">Others can discover and study this deck</span>
          </div>
          <label class="switch">
            <input v-model="isPublic" type="checkbox" />
            <span class="slider round"></span>
          </label>
        </div>

        <div class="action-footer">
          <button class="save-button" @click="handleSave">Save Deck</button>
        </div>
      </main>
    </div>
  </div>
</template>

<style scoped>
.create-deck-view {
  background-color: #0a1628;
  min-height: 100vh;
  color: #ffffff;
}

.container {
  max-width: 600px;
  margin: 0 auto;
  padding: 24px 16px;
}

.view-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 32px;
}

.back-button {
  background: none;
  border: none;
  color: #ffffff;
  font-size: 24px;
  cursor: pointer;
  padding: 8px;
}

.header-title {
  font-size: 20px;
  font-weight: 700;
  margin: 0;
}

.cancel-button {
  background: none;
  border: none;
  color: #00ffd5;
  font-size: 16px;
  font-weight: 500;
  cursor: pointer;
}

.form-content {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.form-group label {
  font-size: 14px;
  font-weight: 600;
  color: #8a9db5;
}

.form-input,
.form-textarea {
  background-color: rgba(0, 255, 213, 0.05);
  border: 1px solid rgba(0, 255, 213, 0.2);
  border-radius: 12px;
  padding: 16px;
  color: #ffffff;
  font-size: 16px;
  transition: border-color 0.2s;
}

.form-input:focus,
.form-textarea:focus {
  outline: none;
  border-color: #00ffd5;
}

.form-textarea {
  min-height: 120px;
  resize: vertical;
}

.tags-container {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 8px;
}

.tag {
  background-color: rgba(0, 255, 213, 0.1);
  color: #00ffd5;
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 14px;
  display: flex;
  align-items: center;
  gap: 8px;
  border: 1px solid rgba(0, 255, 213, 0.2);
}

.remove-tag {
  background: none;
  border: none;
  color: #00ffd5;
  font-size: 18px;
  cursor: pointer;
  padding: 0;
  line-height: 1;
}

.add-tag-input-wrapper {
  display: flex;
  align-items: center;
  background-color: rgba(0, 255, 213, 0.05);
  border: 1px solid rgba(0, 255, 213, 0.1);
  border-radius: 12px;
  padding: 12px 16px;
}

.tag-icon {
  margin-right: 12px;
  color: #8a9db5;
}

.tag-input {
  background: none;
  border: none;
  color: #ffffff;
  font-size: 16px;
  flex: 1;
}

.tag-input:focus {
  outline: none;
}

.toggle-group {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: rgba(0, 255, 213, 0.05);
  border: 1px solid rgba(0, 255, 213, 0.1);
  border-radius: 16px;
  padding: 20px;
  margin-top: 8px;
}

.toggle-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.toggle-label {
  font-size: 16px;
  font-weight: 600;
}

.toggle-hint {
  font-size: 12px;
  color: #8a9db5;
}

/* Toggle Switch Styles */
.switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 24px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(255, 255, 255, 0.1);
  transition: 0.4s;
}

.slider:before {
  position: absolute;
  content: '';
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: 0.4s;
}

input:checked + .slider {
  background-color: #00ffd5;
}

input:checked + .slider:before {
  transform: translateX(26px);
}

.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}

.action-footer {
  margin-top: 32px;
}

.save-button {
  width: 100%;
  background: linear-gradient(135deg, #00ffd5 0%, #00d9b8 100%);
  color: #0a1628;
  border: none;
  padding: 18px;
  border-radius: 30px;
  font-size: 18px;
  font-weight: 700;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
}

.save-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(0, 255, 213, 0.3);
}

.save-button:active {
  transform: translateY(0);
}
</style>
