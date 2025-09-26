# 🦅 Falcon Ledger
*A Red-Team Vulnerability Management & Pentest Notes Application*

Falcon Ledger is a purpose-built tool for red teamers, penetration testers, and security researchers to **organize findings, track vulnerabilities, and manage engagement notes** in one streamlined platform.

---

## ✨ Features
- 📝 **Pentest Notes** — Keep detailed engagement notes with Markdown support.  
- 🗂 **Vulnerability Tracking** — Organize findings with severity, status, and remediation notes.  
- 🔍 **Red-Team Workflow** — Tailored for reconnaissance, exploitation, post-exploitation, and reporting phases.  
- 🔐 **OPSEC-Aware** — Local-first data storage with optional encryption for sensitive notes.  
- 📊 **Dashboard & Metrics** — Track progress across multiple assessments.  
- 📤 **Export & Reporting** — Generate clean reports for clients or internal tracking.  

---

## 🛠️ Installation

### From Source
```bash
git clone https://github.com/your-org/falcon-ledger.git
cd falcon-ledger
composer install
cp .env.example .env # Ask chronopad for .env file
php artisan key:generate
npm install
composer run dev
```