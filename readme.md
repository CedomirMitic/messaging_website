# 💬 Messaging Website

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-4e1184?style=for-the-badge&logo=livewire&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)

> **Live Demo:** [https://messagingwebsite-production.up.railway.app/](https://messagingwebsite-production.up.railway.app/)

## 📝 Project Summary

This project is a **Real-Time Social Network Messaging Application** that allows users to communicate instantly, manage a friends list, share updates via posts, and manage their personal profile with robust security features, including Two-Factor Authentication.

The application is fully deployed and optimized for cloud environments using **Railway**, with media assets served via **Cloudinary CDN**, ensuring high availability, persistent storage, and optimal performance.

## 🛠️ Technology Stack

The application is built using a modern, reactive stack:

| Category | Technology | Version | Purpose |
| :--- | :--- | :--- | :--- |
| **Backend** | PHP | 8.2.12 | Core programming language |
| **Framework** | Laravel Framework | 12.38.1 | Robust MVC web framework |
| **Real-time** | Pusher / laravel-echo | 8.4.0 / 2.2.6 | Instant message delivery and event broadcasting |
| **UI Components** | Flux UI | Premium | Premium Livewire component library for consistent design |
| **Media Storage** | Cloudinary | Latest | Cloud-based image management and delivery (CDN) |
| **Icons** | Lucide / Heroicons | Latest | Clean and scalable vector icons |
| **Frontend Dynamics** | Livewire | v3.6.4 | Full-stack framework for reactive UI development |
| **Styling** | Tailwind CSS | 4.1.11 | Utility-first CSS framework |
| **Build Tool** | Vite | 7.0.6 | Modern frontend tooling and asset bundling |

---

## 💻 Technical Implementation: System Components

### A. Frontend User Interface (UI)

#### 1. Public Access
* **Welcome Page:** Simple landing with a welcoming slogan and navigation links for **Login** and **Register**.
* **Login Page:** Features email/password authentication, **Password Reset** via email, password visibility toggler, and "Remember Me" session management.
* **Register Page:** Secure user onboarding with real-time validation and password confirmation.

#### 2. Authenticated Experience
* **Dashboard (Discovery & Social Feed):** * Displays cards of potential users (non-friends) with instant **"Add Friend"** functionality.
    * A central feed displaying all posts from the user's social circle.
* **Chat System:**
    * **Friends List:** Dynamic sidebar listing all friends with avatars and real-time status.
    * **Active Chat Tab:** Scrollable instant messaging interface featuring the recipient's profile summary and full message history.
* **Navigation:** Integrated settings menu for profile management and secure logout.

#### 3. Profile & Security Management
* **Profile Settings Page:** Allows updating Name, Email, and **Avatar Photo** (Cloud-hosted).
* **Account Control:** Secure options for password updates and permanent account deletion.
* **Two-Factor Authentication (2FA):** Secure setup requiring password confirmation and QR Code scanning via mobile authenticator apps.
* **Appearance:** Built-in theme toggler for Light, Dark, or System preference.
* **Social Interaction Profile:** 
    * **Dynamic User Timeline:** Displays filtered posts for the specific user being viewed.
    * **Friendship Status Logic:** Contextual UI buttons (Add Friend, Friends/Unfriend, or Create Post) based on the relationship.
    * **Cloudinary Integration:** Ensures all profile and post images are persistent and delivered via CDN.

#### 4. Interactive Engagement System
* **Post Interaction:** 
    * **Likes System:** Real-time like/unlike functionality with instant feedback.
    * **Social Proof:** Hoverable "Like" counters showing the list of users who engaged with the post.
* **Notifications Engine:**
    * **Global Alerts:** Real-time Pusher-driven notifications for new likes and messages.
    * **Toast Notifications:** Elegant Flux/Livewire toasts for system actions (e.g., "Post Deleted").

### B. Backend Logic and Real-time Messaging
* **Architecture:** Strictly follows the **Model-View-Controller (MVC)** pattern.
* **Authentication:** Secure session management, profile editing, and automated friend addition on first message.
* **Real-time Communication:** * Implementation of typing indicators and message read receipts.
    * Global notification broadcasting and per-user private chat channels.

---

## 🛡️ Security Implementation

Security is managed using enterprise-level protections offered by Laravel:
* **CSRF Protection:** Automatic token verification for all state-changing requests.
* **XSS Prevention:** Default output escaping via the Blade templating engine.
* **SQL Injection:** Exclusive use of **Eloquent ORM** and prepared statements.
* **Secure Auth:** Bcrypt/Argon2 hashing and HTTP-only cookies.
* **Mass Assignment Protection:** Strict use of $fillable properties on Eloquent models to prevent unauthorized data updates.
* **2FA:** Implemented using TOTP for an extra layer of protection.
* **Route Protection:** Use of Laravel middleware to ensure only authenticated users can access core application features.
* **Cloud & Environment Security:** Secure API key handling via `.env` and private channel authentication for real-time data.

---

## 🗄️ Database Design (Schema)

| Table Name | Fields | Relationships |
| :--- | :--- | :--- |
| **`users`** | `id`, `name`, `email`, `profile_image`, `password`, `two_factor_*` | Primary user data |
| **`messages`** | `id`, `sender_id`, `receiver_id`, `message`, `read_at` | `sender/receiver` $\to$ `users.id` (1:M) |
| **`friend_lists`** | `friend_id`, `user_id` | Pivot table for M:N friendship |
| **`posts`** | `id`, `user_id`, `title`, `content`, `image` | `user_id` $\to$ `users.id` (1:M) |
| **`likes`** | `id`, `user_id`, `post_id` | Pivot table: `users.id` (M:N) `posts.id` |
| **`notifications`** | `id`, `type`, `notifiable_id/type`, `data` | Polymorphic table for system alerts |

---

Developed by Cedomir Mitic