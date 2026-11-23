# Speaker notes — Capstone 2 Final Defense

**Presenter (first / opening):** Rhem A. Sumodlayon  
**Deck:** `docs/.capstone/Presentation/index.html` (17 slides)  
**Style:** Formal English.  
**Do not say:** walk-in PIN digits, a measured cut in waiting time, “the Guard logs in,” or student names on the ticket or public display.

---

## Slide 1 — Title

Good afternoon, members of the oral defense panel, Sir Carlos S. Gerardo, our adviser, Dean Daryll A. Cabagay, Program Head Zoren Estrada Bajao, faculty, and fellow students.

I am **Rhem A. Sumodlayon**, the group leader, together with our group: Mike D. Bedrona, Niño Angelo M. Malicay, Omar O. Bayabao, and Ron Matthew F. Arquisola, Bachelor of Science in Information Systems, College of Computer Studies, PECIT.

Today, September 16, 2026, here at the AVR Conference Room, New Building, PECIT, Butuan City, we present our Capstone 2 Final Defense.

The title of our study is:

**Standalone Kiosk-Based Queuing Management System with Thermal Printer for Student Service Offices at Philippine Electronics and Communication Institute of Technology (PECIT).**

In short: a kiosk, a 58mm ticket, and shared staff windows, to replace the manual first-come-first-served line at Cashier, Registrar, and DMO.

With your permission, we would like to begin our presentation.

---

## Slide 01 — Project identification

This slide identifies the project.

The full title is Standalone Kiosk-Based Queuing Management System with Thermal Printer for Student Service Offices at Philippine Electronics and Communication Institute of Technology, or PECIT.

The proponents are Mike D. Bedrona, Niño Angelo M. Malicay, Omar O. Bayabao, myself, Rhem A. Sumodlayon, and Ron Matthew F. Arquisola.

Our adviser is **Carlos S. Gerardo** of the College of Computer Studies.

Leadership: Dean Daryll A. Cabagay, MSIT, and Program Head Zoren Estrada Bajao.

This is our Capstone 2 final defense, today, in the AVR Conference Room.

Next, we will explain the problem of lining at the offices.

---

## Slide 02 — Background / Problem

At PECIT, Cashier, Registrar, and DMO handle student payments, records, and data transactions.

Today, clients wait in a **manual first-come-first-served line**. They do **not** receive a queue ticket. Whoever arrives first is served first. That is the only rule.

The usual effects are: it is unclear who is next, line cutting happens, priority clients are hard to serve fairly, the waiting area gets crowded, and there is no shared view of who is being served and who is waiting.

Staff also cannot coordinate several windows from one organized queue. Cashier 1, 2, and 3, for example, still depend on the physical line.

The problem is not the absence of computers. Arrival order in a physical line is the only queue rule.

That is why we built a **standalone LAN kiosk** that still runs when the internet is down.

---

## Slide 03 — Statement of the Problem

The general problem of the study is:

How can a standalone kiosk-based queuing management system replace first-come-first-served lining that currently uses no queue ticket with an organized, monitored, and coordinated queue at PECIT?

There are five specific problems.

First: how can Cashier, Registrar, and DMO be organized instead of a physical line with no ticket, where line cutting can happen.

Second: how can Student ID or an authorized walk-in be checked **before** a number is issued.

Third: how can clients have a clear queue reference when the current process issues no ticket, and how can priority stay fair when several windows share one queue.

Fourth: how can clients and windows see who is serving and who is waiting.

Fifth: how can we evaluate functionality, usability, efficiency, reliability and accuracy, and satisfaction.

---

## Slide 04 — Project Objectives

The general objective is to design, develop, and implement a standalone kiosk-based queuing system with a thermal printer for PECIT student service offices.

The specific objectives are the same five.

One: develop the standalone kiosk for Cashier, Registrar, and DMO.

Two: confirm with Student ID or authorized kiosk-PIN walk-in **before** numbering.

Three: print a 58mm ticket and call **2 Priority → 1 Regular** across shared windows.

Four: provide a real-time public display and staff monitoring.

Five: evaluate with testing, expert review, and UAT, using an ISO/IEC 25010-aligned form.

Every later result maps back to these five. This study does **not** claim a measured drop in waiting time.

---

## Slide 05 — Scope and Limitations

This slide shows what is in the study and what is not.

**In scope:** students, walk-ins, staff, and the administrator. Offices: Cashier, Registrar, and DMO. Four modules: Kiosk, Staff, Administrator, and Display. The platform is LAN, Laravel 12, MySQL, and the 58mm XP-Q90EC printer. Cashier 1, 2, and 3 share one waiting queue.

**Limitations:** no online reservation, mobile app, SMS, or cloud admin. Not all campus offices. **RFID/NFC is not in this build** — we list it as later work. A student cannot take a second ticket while one is still waiting, serving, or held. We do not claim a measured cut in waiting time.

This system is for PECIT Cashier, Registrar, and DMO on a local LAN — not an online reservation product.

---

## Slide 06 — Proposed Solution

This is the proposed solution.

The client starts at the kiosk in three steps: service, Regular or Priority, then confirm. The kiosk prints an **58mm ticket with the number only**. Staff windows call the next ticket using **2 Priority → 1 Regular**. The public display shows numbers only.

Compared with the existing process:

- Stand in a physical line → start at the kiosk.
- Staff call whoever is next in line → Call Next by system order, with Hold and Call Held.
- No ticket and no public board → number-only ticket and display.

A name may appear on the confirm screen so the student can check it. **It is never printed. It is never shown on the public display.**

---

## Slide 07 — System Architecture

This figure is our system architecture.

On the LAN we have the kiosk and thermal printer, the staff PCs, the public display, and the administrator. In the center are the Laravel application and MySQL.

If the internet drops, the queue can still run on the local network. No cloud service is required for daily operations.

That is what we mean by a standalone kiosk-based system.

---

## Slide 08 — System Development Methodology

We used the Waterfall model because the campus process was already defined. We locked the queue rules first, then we built and tested them.

The five phases are:

1. **Requirements** — the manual line, ID confirm, walk-in PIN, and shared cashiers.
2. **Design** — LAN, 58mm print, 2 Priority → 1 Regular, Hold, and a number-only display.
3. **Implementation** — Laravel 12 modules: kiosk, staff, admin, and display.
4. **Testing** — functional tests, expert review, and UAT.
5. **Deployment** — standalone XAMPP on the PECIT LAN.

Four modules: Student Kiosk, Staff, Administrator, and Queue Display.

---

## Slide 09 — Major System Features

These are live captures from the running system.

**Kiosk:** three steps. Student ID is entered on confirm. Walk-in uses a corner PIN every time, with no Student ID. Many walk-in tickets are allowed.

**Staff:** Call Next, Hold, and a service timer. Call order is **2 Priority → 1 Regular**. Staff can see the student name, or Walk-in with the reason.

**Display:** Now Serving and waiting. **No names. No held tickets.**

**Admin:** users, students, the walk-in PIN in settings, and reports on **done tickets only**. The Guard role **cannot log in**.

Hold is not Complete. When a ticket is held, it leaves Now Serving.

---

## Slide 10 — Technology Stack

The software stack is PHP 8.2, Laravel 12, and Blade, with Vite and Tailwind on the front end. Fonts and icons are local. There is no CDN.

The database is MySQL or MariaDB, database name `queuing_system`.

Hardware: a touch kiosk, the XP-58 (XP-Q90EC) 58mm thermal printer, a public TV, and staff PCs.

Hosting is local XAMPP on the PECIT LAN — not a cloud host.

We did **not** use a public cloud API, an SMS gateway, or an AI/ML model.

Laravel keeps the queue rules on the server: fair claim, one open student ticket, and the PIN check. Local assets keep the kiosk usable even when the internet is down.

---

## Slide 11 — Testing and Evaluation

We keep two evaluations apart.

**Chapter 3** is a questionnaire to **50** students and parents about the **current line**, before we built the system. Those tables stay in Chapter 3.

**Chapter 4** is evaluation of the **implemented system**: functional tests at Cashier, Registrar, and DMO, the student system form, and one IT expert.

**ISO/IEC 25010** is the basis of the evaluation form only. It is **not** a certification that the system is “ISO compliant.”

There was **one IT expert**. Same five areas, 25 items. Overall **5.00**. Those scores are **not mixed** into the user table.

The student form is **20 students, 5 parents, and 5 walk-in visitors**, N = 30.

Efficiency is a perception score, not a measured cut in waiting time.

---

## Slide 12 — Results and Findings

These are the Chapter 4 results.

**Combined users, Table 4.3:** Functionality 4.93, Usability 4.98, Efficiency 4.97, Reliability and Accuracy 4.93, User Satisfaction 4.99. **Overall 4.96, Strongly Agree.**

By form: Cashier 5.00, Registrar 5.00, DMO 4.92, student form 4.91.

The **IT expert** is Table 4.4, **5.00**, not mixed into 4.96. The expert said the system is ready to deploy, that network configuration must be set up because PECIT uses a cloud-based network, and that the UI should be modernized. UI change is **future work**. We keep the three-step kiosk.

We do **not** claim a percentage reduction in waiting time.

If asked whether waiting time improved: efficiency scored 4.97 as a user rating. We did not run a controlled before-and-after time study, so we will not state a percentage.

---

## Slide 13 — Objectives vs. Results

We now map each objective to what was built and how we know it works. All five are **Achieved**.

1. Organize Cashier, Registrar, and DMO — shared service queues and a 3-step kiosk. Evidence: functional tests of issue and Call Next.
2. Confirm before numbering — Student ID lookup and kiosk-PIN walk-in. Evidence: unknown ID and a second open ticket are rejected.
3. Ticket and fair priority — 58mm number-only print and **2 Priority → 1 Regular**. Evidence: print path, and no duplicate claim when two cashiers call at once.
4. Real-time monitoring — display and staff panel use the same call order. Evidence: names and held tickets stay off the public view.
5. Evaluate the system — tests, expert review, and UAT. Evidence: combined users **4.96**, IT expert **5.00**, not mixed.

---

## Slide 14 — Conclusion

In conclusion:

First, a standalone kiosk can replace the manual line at the selected offices.

Second, confirmation — Student ID or PIN walk-in — works before numbering.

Third, the ticket is a clear reference, and 2 Priority → 1 Regular stays fair on shared windows.

Fourth, the display and staff panel give a real-time, number-only public view.

Fifth, combined users 4.96 and the IT expert 5.00 support acceptance under ISO/IEC 25010-aligned items. That form is an evaluation form, not a certification.

This is a practical replacement for the manual line. We do **not** claim a measured cut in waiting time.

---

## Slide 15 — Recommendations / Future Enhancements

**Now — already implemented, for the school to use:**

Deploy the kiosk, printer, display, staff PCs, and LAN as specified. Before go-live, prepare network setup for PECIT’s cloud-based network, as the IT expert noted. Orient staff on Confirm errors, the walk-in PIN, Hold, and Call Held. Keep the student list and PIN current, and back up MySQL. Use **done-only** reports. Do not treat Hold as Complete.

**Later — not built in this version:**

Record actual wait and service times if PECIT wants a measured comparison. **Modernize** the staff and kiosk UI, but keep the three-step kiosk and number-only print and display. Add **RFID/NFC tap** at confirm as another way to read a Student ID — print and display stay number-only. Optional SMS or QR status, still with no names. Remote reserve would still require ID or authorized walk-in. More offices only if they share the same local rules.

---

## Slide 17 — Thank you / Q&A

That concludes our presentation.

Thank you to the members of the oral defense panel, to Sir Carlos S. Gerardo, to Dean Daryll A. Cabagay, to Program Head Zoren Estrada Bajao, and to everyone who listened.

We are now open for questions, comments, and clarifications.

---

## Likely questions — short answers

**Why standalone / LAN only?**  
Daily queue operations must still run without internet. Cloud reservation is out of scope.

**Why is there no name on the ticket?**  
Privacy. Student ID is used at confirm and while staff are serving. The public display shows numbers only.

**What is a walk-in?**  
If there is no Student ID, a corner control asks for a PIN every time, checked on the server. Many open walk-in tickets are allowed. The Guard role cannot log in.

**Why 2 Priority → 1 Regular?**  
So priority is fair, but regular clients are not blocked indefinitely. Shared across Cashier 1–3.

**Hold vs Complete?**  
Hold: set aside, free the window, leave Now Serving, **not counted as served**. Call Held: resume, skip 2P→1R.

**Is this ISO/IEC 25010 certified?**  
No. We used it as an **evaluation form**. Combined users 4.96. One IT expert scored 5.00 and is not mixed into that table.

**RFID/NFC?**  
Not yet. Listed as later work, tap at confirm, names still off print and display.

**Did waiting time go down?**  
We do not claim a measured percentage. Efficiency in UAT is a perception score.
