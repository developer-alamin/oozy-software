<!-- src/Pages/Contact.vue -->
<template>
    <div>
        <h1>Contact Us</h1>
        <form @submit.prevent="submitForm">
            <div>
                <label for="name">Name</label>
                <input
                    v-model="name"
                    id="name"
                    type="text"
                    placeholder="Your Name"
                />
            </div>
            <div>
                <label for="email">Email</label>
                <input
                    v-model="email"
                    id="email"
                    type="email"
                    placeholder="Your Email"
                />
            </div>
            <div>
                <label for="message">Message</label>
                <textarea
                    v-model="message"
                    id="message"
                    placeholder="Your Message"
                ></textarea>
            </div>
            <button type="submit">Submit</button>
        </form>
    </div>
</template>

<script setup>
import { ref } from "vue";

const name = ref("");
const email = ref("");
const message = ref("");

const submitForm = () => {
    alert(`Message sent by ${name.value}: ${message.value}`);
    // In a real-world app, you'd submit this form data to your backend here.
};
</script>

<style scoped>
form {
    max-width: 600px;
    margin: 0 auto;
}
div {
    margin-bottom: 10px;
}
label {
    display: block;
    margin-bottom: 5px;
}
input,
textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}
button {
    padding: 10px 20px;
    background-color: #4caf50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
button:hover {
    background-color: #45a049;
}
</style>
