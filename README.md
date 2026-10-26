# 🍯 Digital Memory Jar — Complete College Mini Project

> *"Little moments, forever memories."*

A full-stack, creative, dreamy, and beginner-friendly web application built with **HTML5, CSS3, Vanilla JavaScript, PHP 8, and MySQL** for XAMPP.

Digital Memory Jar is a personal haven where users can save their happy memories, achievements, special moments, travel experiences, friendship memories, family joys, and positive messages. Whenever they need gentle encouragement or nostalgic happiness, they can open their virtual jar and rediscover a random cherished memory.

---

## 🌸 Color Palette & Aesthetics

Designed with a soft, nostalgic, cute, and modern aesthetic:

- **Background:** Warm cream (`#FFF8EF`)
- **Primary:** Soft lavender (`#C8B6E2`)
- **Secondary:** Pastel pink (`#F4C7D9`)
- **Accent:** Butter yellow (`#FFE8A3`)
- **Text:** Dark plum (`#49364F`)
- **Supporting:** Sage green (`#C8D8C0`) & Peach (`#FAD4C0`)
- **Typography:** *Poppins* (headings) & *Nunito* (body text)

---

## ✨ Key Features & Pages

### 1. Login & Registration (`index.php`)
- Beautiful tabbed interface for **Sign In** and **Create Jar**.
- Client-side (JS) and server-side (PHP) validation.
- Email uniqueness check preventing duplicate accounts.
- Password hashing using `password_hash()` and verification with `password_verify()`.
- PHP session management with session regeneration to prevent fixation.
- Password visibility toggle (👁️).

### 2. Home Page (`home.php`)
- Personalized welcoming greeting with user's name.
- Dynamic **Memory Counter** showing the actual count of memories saved by the logged-in user in MySQL.
- **Pure CSS Glass Memory Jar Illustration:**
  - Handcrafted cork lid, curved neck, tie ribbon with "Moments" tag.
  - Glass specular reflections and gentle floating animation.
  - Dynamically renders colorful folded origami notes inside the jar based on the user's saved memories.
  - Gentle glowing fireflies inside the glass.
- Quick navigation: **Open My Jar**, **Add a Memory**, **My Memories**.
- Warm welcoming message card.

### 3. Open My Jar — Main Creative Feature (Modal on `home.php`)
- Clicking **"Open My Jar"** triggers:
  1. A playful, gentle shaking animation on the glass jar.
  2. Floating sparkles and confetti particles burst effect.
  3. AJAX request to `api/get-random-memory.php` retrieving a random memory for the current user from MySQL.
  4. Revealing the memory card with its category badge, mood emoji, date, title, and full story.
  5. **"Pick Another Memory"** button that fetches another random memory smoothly without page reload.
  6. Empty state handling if the user has 0 memories with an invitation button to tuck their first memory.

### 4. Add a Memory (`add-memory.php`)
- Form fields:
  - Memory Title (max 150 chars).
  - Category (Happy Moment, Achievement, Friendship, Family, Travel, Positive Message, Other).
  - Date (date picker, defaulted to today).
  - Mood selector with interactive emoji pills (😊 Happy, 🥰 Loved, 🌈 Hopeful, 🎉 Excited, 😌 Peaceful, 💖 Grateful).
  - Memory Story / Description with live character counter.
- Dual-layer validation (JavaScript + PHP prepared statements).
- Strictly binds `user_id` to the session to prevent unauthorized cross-user insertions.
- Friendly success message: *"A little memory has been safely tucked into your jar! ✨"*

### 5. My Memories Collection (`memories.php`)
- Displays all memories belonging strictly to the logged-in user as cards.
- **Live Instant Search:** filter memories by title or keywords on the fly.
- **Category Filter:** filter by specific categories.
- **Sort Dropdown:** Newest First or Oldest First.
- **View Full Memory Modal:** read the complete story.
- **Edit Memory (`edit-memory.php`):** update memory with server-side ownership verification.
- **Delete Memory (`delete-memory.php`):** sweet confirmation modal followed by animated removal from the jar.
- Empty states for:
  - Brand new jar (0 memories).
  - No matching search/filter results.

---

## 🗄️ MySQL Database Structure

**Database Name:** `digital_memory_jar`

### Table: `users`
| Column | Type | Attributes | Description |
|---|---|---|---|
| `id` | INT | Primary Key, Auto Increment | Unique user identifier |
| `name` | VARCHAR(100) | NOT NULL | User's full name |
| `email` | VARCHAR(150) | NOT NULL, UNIQUE | User's email address |
| `password` | VARCHAR(255) | NOT NULL | Hashed password |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Registration timestamp |

### Table: `memories`
| Column | Type | Attributes | Description |
|---|---|---|---|
| `id` | INT | Primary Key, Auto Increment | Unique memory identifier |
| `user_id` | INT | NOT NULL, Foreign Key | References `users.id` (ON DELETE CASCADE) |
| `title` | VARCHAR(150) | NOT NULL | Title of the memory |
| `description` | TEXT | NOT NULL | Complete memory story |
| `memory_date` | DATE | NOT NULL | Date when the memory happened |
| `category` | VARCHAR(50) | NOT NULL | Memory category |
| `mood` | VARCHAR(30) | NULLABLE | Mood emoji label |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Created timestamp |

---

## 🚀 How to Run in XAMPP

1. **Start Apache & MySQL:**
   - Open the **XAMPP Control Panel**.
   - Click **Start** next to **Apache**.
   - Click **Start** next to **MySQL**.

2. **Database Setup (Automatic & Manual options):**
   - **Automatic Setup:** `config/db.php` will automatically create the database `digital_memory_jar` and both tables (`users` & `memories`) as soon as you open the site!
   - **Manual Import (Optional with Seed Data):**
     1. Open [http://localhost/phpmyadmin](http://localhost/phpmyadmin) in your browser.
     2. Click the **Import** tab.
     3. Choose the file `database.sql` located inside `d:\xampp\htdocs\digital-memory-jar\`.
     4. Click **Import**.

3. **Open the Website:**
   - Open your web browser and navigate to:
     ```
     http://localhost/digital-memory-jar/
     ```

4. **Starter Test Credentials (from database.sql):**
   - **Email:** `demo@memoryjar.com`
   - **Password:** `password123`
   *(Or register your own new account directly on the Sign In / Create Jar page!)*

---

## 📂 Project Directory Structure

```
digital-memory-jar/
├── api/
│   └── get-random-memory.php    # Secure AJAX endpoint for "Open My Jar"
├── assets/
│   ├── css/
│   │   └── style.css            # Dreamy color palette, glass jar illustration, animations
│   └── js/
│       └── main.js              # Interactivity, validations, AJAX & modal controllers
├── config/
│   └── db.php                   # PDO database connection & auto-table creator
├── includes/
│   ├── auth_check.php           # Session guard for protected pages
│   ├── header.php               # Common navigation bar & Google fonts
│   └── footer.php               # Common footer & scripts
├── add-memory.php               # Add a memory page
├── database.sql                 # SQL schema & demo seed data
├── delete-memory.php            # Secure memory deletion handler
├── edit-memory.php              # Edit memory page
├── home.php                     # Home page with CSS glass jar & random reveal modal
├── index.php                    # Login and Registration page
├── logout.php                   # Session logout handler
├── memories.php                 # My Memories showcase page with search/filter/sort
└── README.md                    # Project documentation
```

---

## 🛡️ Security Features Implemented
- **SQL Injection Prevention:** 100% prepared statements (`$pdo->prepare()`) with bound parameters.
- **Password Security:** BCRYPT hashing with `password_hash()` and `password_verify()`.
- **XSS Protection:** Context-safe output escaping (`htmlspecialchars()`).
- **Authorization & Ownership Checks:** Strict server-side verification (`WHERE id = :id AND user_id = :user_id`) on all viewing, editing, deleting, and random-fetch operations.
- **Session Protection:** Session regeneration on authentication, complete cookie cleanup on logout.
- **Graceful DB Connection Fallback:** If MySQL is not started in XAMPP, a friendly guidance screen informs the user instead of raw PHP fatal errors.
