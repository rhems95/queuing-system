# Defense slides — PECIT Queuing System

**Talk length:** about 12–18 minutes (21 slides).  
**Title to use:** Standalone Kiosk-Based Queuing System with Thermal Printer for Student Service Offices at PECIT  
**Researchers:** Mike D. Bedrona, Niño Angelo M. Malicay, Omar O. Bayabao, Rhem A. Sumodlayon, Ron Matthew F. Arquisola  
**Program:** Bachelor of Science in Information Systems, College of Computer Studies, PECIT  

**Do not say on a slide:** walk-in PIN digits, student names on the ticket or public display, a measured cut in waiting time, or “the Guard logs in.”

**Figures:** Yes — include the system. Put **live screenshots** on slides 12–16 (do not use the old Word wireframes).

| Slide | Figure (capture from the running system) | What the panel should see |
| --- | --- | --- |
| 12 | Simple LAN diagram (kiosk, printer, staff, display, admin, MySQL) | It is standalone |
| 13 | Kiosk confirm step + a printed 58mm ticket | 3 steps; **number only** on paper |
| 14 | Staff window while serving (name is OK here) + Hold list | Call Next / Hold / timer |
| 15 | Public display (numbers only) | No names on the TV |
| 16 | Admin dashboard | Live waiting in staff call order |

Crop out the walk-in PIN pad. Do not screenshot a student name on the public display. If the manuscript figures are still placeholders, recapture before the defense.

---

## Slide list (quick)

| No. | Slide title |
| --- | --- |
| 1 | Title |
| 2 | Presentation outline |
| 3 | Introduction / setting |
| 4 | Existing process |
| 5 | Statement of the problem |
| 6 | Specific problems |
| 7 | Objectives |
| 8 | Scope and delimitation |
| 9 | Significance |
| 10 | Related studies (gap) |
| 11 | Methodology |
| 12 | System architecture |
| 13 | Kiosk and ticket |
| 14 | Staff, Hold, and fair call |
| 15 | Public display |
| 16 | Administrator |
| 17 | Testing and UAT results |
| 18 | Findings vs. five objectives |
| 19 | Conclusions |
| 20 | Recommendations |
| 21 | Thank you / Q&A |

---

## Slide 1 — Title

**On slide**

- Standalone Kiosk-Based Queuing System with Thermal Printer for Student Service Offices at Philippine Electronics and Communication Institute of Technology (PECIT)
- A Capstone Project
- Bachelor of Science in Information Systems
- Mike D. Bedrona · Niño Angelo M. Malicay · Omar O. Bayabao · Rhem A. Sumodlayon · Ron Matthew F. Arquisola
- September 2026

**Say:** This capstone replaces the manual first-come-first-served line at PECIT’s Cashier, Registrar, and DMO with a standalone kiosk, 58mm ticket, and shared staff windows.

---

## Slide 2 — Outline

**On slide**

1. Introduction and problem  
2. Objectives and scope  
3. Related studies  
4. Methodology and design  
5. Implementation  
6. Results  
7. Conclusions and recommendations  

---

## Slide 3 — Introduction

**On slide**

- PECIT student offices handle payments, records, and DMO transactions.
- Clients currently wait in a **manual first-come-first-served line**.
- Problems: unclear order, line cutting, weak priority handling, crowding, no shared view of who is waiting.
- The study built a **LAN-based kiosk system** that does not need continuous internet for daily queue work.

**Say:** The problem is not “no computer on campus.” It is that arrival order in a physical line is the only queue rule.

---

## Slide 4 — Existing vs proposed process

**On slide**

| Existing | Proposed |
| --- | --- |
| Stand in a physical line | Start at the kiosk |
| Staff call by who is next in the line | Staff Call Next by system order |
| No ticket | 58mm number-only ticket |
| No public board | Public display: numbers only |
| Hard to serve priority clients | Regular or Priority, then 2 Priority → 1 Regular |

**Say:** The kiosk is still three steps: service → Regular or Priority → confirm.

---

## Slide 5 — General problem

**On slide**

How can a standalone kiosk-based queuing management system replace first-come-first-served lining that currently uses no queue ticket with an organized, monitored, and coordinated queue at PECIT?

---

## Slide 6 — Specific problems (5)

**On slide**

1. How can Cashier, Registrar, and DMO transactions be organized instead of a physical line with no ticket and line cutting?  
2. How can a request be checked (Student ID or authorized walk-in) **before** a number is given?  
3. How can clients have a clear queue reference when the current process issues no ticket, and how can priority be fair when several windows share one queue?  
4. How can clients and windows see who is serving and who is waiting?  
5. How can the system be evaluated (functionality, usability, efficiency, reliability and accuracy, satisfaction)?  

---

## Slide 7 — Objectives (match the 5 problems)

**On slide**

1. Develop a standalone kiosk system for Cashier, Registrar, and DMO.  
2. Confirm with Student ID **or** authorized kiosk-PIN walk-in before numbering.  
3. Print a 58mm ticket and call **2 Priority → 1 Regular** across shared windows.  
4. Provide a real-time public display and staff monitoring.  
5. Evaluate with testing, expert review, and UAT (ISO/IEC 25010-aligned).  

**Say:** Every later slide maps back to these five. We do **not** claim a measured percentage drop in waiting time.

---

## Slide 8 — Scope and delimitation

**On slide — In scope**

- Offices: Cashier, Registrar, DMO  
- Kiosk (3 steps), walk-in PIN path, 58mm print  
- Staff: Call Next, Recall, Complete, Hold, Call Held, per-window timer  
- Cashier 1–3 share one waiting queue  
- Admin: users, students, kiosk PIN, done-only reports  
- LAN / Laravel 12 / MySQL  

**On slide — Out of scope**

- Online reservation, mobile app, SMS/email, cloud admin  
- All campus offices  
- RFID/NFC capable  
- A second student ticket while one is still in the queue  

---

## Slide 9 — Significance (macro → micro)

**On slide**

- **PECIT** — localized LAN queue, no need for always-on internet  
- **Administrator** — users, student allowlist, PIN, done-only reports  
- **Staff** — fair call, Hold, name or Walk-in (reason) while serving  
- **Students / parents** — ticket + public numbers + optional ETA  
- **Future researchers** — base for other campus queue studies  

---

## Slide 10 — Related studies and gap

**On slide**

- PH HEI queues: Maala [1], Espinosa [3], Pantonial [4], Ramoga [5], Concepcion [11]  
- Localized campus systems: Grepon [7]; LAN/low-connectivity: Linhalis [10]  
- Quality model for evaluation: ISO/IEC 25010 [8]  

**Gap (one sentence):** Existing systems are useful, but PECIT still needed one **standalone** design that combines kiosk + thermal print + Student ID confirm + PIN walk-in + 2P→1R multi-counter + number-only display.

**Say:** We did not copy an online-appointment or ticketless-ID campus product. We built for PECIT’s offices and LAN.

---

## Slide 11 — Methodology

**On slide**

- **Model:** Waterfall — requirements, design, implementation, testing, deployment  
- **Modules:** Student Kiosk · Staff · Administrator · Queue Display  
- **Rules implemented:** student blocked from a new ticket while still in queue · walk-in many · Hold ≠ Complete · reports = **done** only  
- **Evaluation:** functional tests, one technical expert, UAT (ISO/IEC 25010-aligned, not a certification)  

---

## Slide 12 — Architecture (LAN)

**On slide**

```text
Kiosk + 58mm printer
        \
         LAN  →  Application (Laravel) + MySQL
        /
Staff PCs          Public display          Admin PC
```

**Say:** If the internet drops, the queue can still run on the local network.

---

## Slide 13 — Kiosk and ticket (Objectives 1–3)

**On slide**

- Step 1: Cashier, Registrar, or DMO (Promissory Notes hidden on the public kiosk)  
- Step 2: Regular or Priority  
- Step 3: Student ID keypad → lookup must succeed → Confirm  
- Walk-in: corner control → PIN every time → no Student ID · many walk-ins allowed  
- Ticket: **number only** · optional one line `Estimated Time: N minutes` if history is enough  
- Printer: 58mm XP-58 (XP-Q90EC)  

**Say:** The name may appear on confirm so the student can check it. It is never printed.

---

## Slide 14 — Staff, fair call, Hold (Objectives 1, 3, 4)

**On slide**

- Call Next locks the service, then claims the next ticket (no duplicate on Cashier 1 and 2)  
- Order: **2 Priority → 1 Regular** across all windows of that service  
- Each window: own open ticket + own timer  
- **Hold:** ticket leaves Now Serving; not counted as served  
- **Call Held:** resume at this window; skips 2P→1R  
- Staff sees **student name** or **Walk-in (reason)**  

---

## Slide 15 — Public display (Objective 4)

**On slide**

- Now Serving + waiting (same fair order, max 10)  
- No names · no held tickets · voice optional  
- Figure F — public display  

---

## Slide 16 — Administrator (Objective 4)

**On slide**

- Users (one staff per window; Guard cannot log in)  
- Students (allowlist / CSV)  
- Kiosk PIN in settings  
- Reports: avg wait and service from **done** tickets only  
- Figure G — admin dashboard  

---

## Slide 17 — Testing and UAT (Objective 5)

**On slide**

**Table — Combined users (Table 4.3; expert in Table 4.4 only)**

| Area | Mean | Interpretation |
| --- | ---: | --- |
| Functionality | 4.93 | Strongly Agree |
| Usability | 4.98 | Strongly Agree |
| Efficiency | 4.97 | Strongly Agree |
| Reliability and Accuracy | 4.93 | Strongly Agree |
| User Satisfaction | 4.99 | Strongly Agree |
| **Overall** | **4.96** | **Strongly Agree** |

**Also on slide:** Cashier 5.00 · Registrar 5.00 · DMO 4.92 · Student form 4.91 (N = 30) · IT expert **5.00** (not mixed). Expert: ready to deploy; set up PECIT’s cloud-based network; modernize UI (future work).

**Say:** Efficiency is a **perception** score. We do **not** report a timed before-and-after waiting-time study. Chapter 3 (N = 50) is the current line and is not mixed with these scores.

---

## Slide 18 — Findings vs. objectives

**On slide**

| Obj. | What we showed |
| --- | --- |
| 1 | Shared queues + kiosk organize Cashier / Registrar / DMO |
| 2 | Confirm blocks unknown ID and a second ticket only while the student still has one in the queue |
| 3 | Number-only 58mm ticket; 2P→1R; no double claim |
| 4 | Display and staff show the same fair waiting order; held/names hidden publicly |
| 5 | Functions tested; users **4.96**; IT expert **5.00** (not mixed) |

---

## Slide 19 — Conclusions

**On slide**

1. A standalone kiosk can replace the manual line at the selected offices.  
2. Confirmation (Student ID or PIN walk-in) works before numbering.  
3. The ticket is a clear reference; 2P→1R is fair on shared windows.  
4. Display + staff panel give a real-time number-only view.  
5. Users 4.96 and IT expert 5.00 support acceptance under ISO/IEC 25010-aligned items.  

**Closing line:** Practical replacement for the manual line. **No claimed measured cut in waiting time.**

---

## Slide 20 — Recommendations

**On slide**

- Deploy kiosk, printer, display, staff PCs, and LAN as specified.  
- Orient staff on Confirm errors, PIN walk-in, Hold / Call Held.  
- Keep student allowlist and PIN current; back up the local database.  
- Use done-only reports; do not treat Hold as Complete.  
- Later: record actual wait/service times if PECIT wants a measured comparison.  
- Future work (not built): SMS, QR status, remote reserve, more offices — still **no names** on print/display.  

---

## Slide 21 — Thank you

**On slide**

- Thank you  
- Questions  
- Researchers + PECIT BSIS / College of Computer Studies  

**If asked “Did waiting time go down?”**  
Answer: Combined-user efficiency was 4.97 Strongly Agree as a user rating. We did not run a controlled time study, so we do not state a percentage.

**If asked “Why no name on the ticket?”**  
Answer: Privacy. ID is for confirm and for staff while serving. Print and public display stay numbers only.

**If asked “What about people without a student ID?”**  
Answer: Kiosk walk-in after the PIN in settings is accepted. Many walk-in tickets may be open. Guard cannot log in.

---

## Demo order (if the panel wants a live run)

1. Kiosk: Cashier → Regular → Student ID `2024-0001` → Confirm → print (number only).  
2. Same ID again while still waiting/serving/held → should fail. After Complete, the same ID may get another ticket.  
3. Staff Cashier 1: Call Next → name visible → Hold → number leaves display.  
4. Call Held → timer starts again → Complete.  
5. Display: numbers only, no held.  
6. Optional: walk-in overlay (do not read the PIN aloud if the room is public).  

Demo IDs in the dump: `2024-0001` … `2024-0005`.
