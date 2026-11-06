# CHAPTER 4

## SYSTEM IMPLEMENTATION, RESULTS, AND DISCUSSION

This chapter presents the implementation of the Standalone Kiosk-Based Queuing Management System with Thermal Printer, the modules and interfaces placed in operation at PECIT, the testing and evaluation results, and the discussion of those results against the five specific objectives. The implemented system replaces the manual first-come-first-served line at the Cashier, Registrar, and Data Management Office (DMO) with a standalone LAN-based kiosk process.

### 4.1 System Overview

The system was developed and implemented for the selected student service offices of the Philippine Electronics and Communication Institute of Technology (PECIT), specifically the Cashier, Registrar, and DMO. It organizes queue generation, ticket issuance, fair multi-window calling, public monitoring, and administrative recording.

The system operates on the institution LAN. One mini PC (Intel Core i5 8th Generation, 8 GB RAM) serves as both the public kiosk host and the application server. Attached to that mini PC are a touchscreen monitor, a 58mm USB thermal printer (XP-58 / XP-Q90EC), a USB RFID/NFC reader (ACR122U-A9), and a GSM/LTE USB module (SIM7600G-H). Staff workstations, an administrator computer, a public display, and a local MySQL/MariaDB database connect over the LAN. Essential queue operations do not require continuous internet connectivity [11]. GSM SMS is optional and uses the local SIM7600G-H module, not a public cloud SMS API. When the thermal printer is out of paper, the staff dashboard shows a paper-out alert.

A student completes three kiosk steps: service, Regular or Priority, and confirmation. During confirmation the Student ID is typed or read from an enrolled RFID/NFC card and validated against the authorized student list, and a second open ticket for the same ID on the same day is rejected. Clients who do not bring the card may still type the Student ID. Clients who cannot use a Student ID may request a walk-in ticket at the same kiosk after the walk-in PIN stored in settings is accepted. **Notify me by SMS** is off by default. Students do not type a mobile number; notices use the number stored on the student record. If no mobile is on file, the control stays off and disabled and shows **No SMS number on file**; print still proceeds. Walk-in clients may enter a number only on the PIN overlay. The printed ticket, public display, and SMS remain number-only. The student name may appear on confirmation and to staff while serving.

Staff call tickets by 2 Priority → 1 Regular across windows that share a service. Cashier 1, Cashier 2, and Cashier 3 share one Cashier waiting queue while keeping independent open calls and timers. Staff may Recall, Complete, Hold, or Call Held. Administrators manage users and student records, set the kiosk walk-in PIN, may issue walk-in tickets, and generate reports from completed transactions only. The Guard role is the kiosk issuer record and cannot log in.

### 4.2 System Features

The implemented system provides the following major features:

Service selection. The public kiosk presents Cashier, Registrar, and DMO. Promissory Notes is hidden from the public list.

Priority selection. The client chooses Regular or Priority. Call Next later applies 2 Priority → 1 Regular.

Transaction confirmation. The third kiosk step requires a valid Student ID lookup before Confirm is enabled, by keypad or RFID/NFC tap, or an authorized walk-in path after the PIN is accepted.

Automatic queue-number generation. After a valid confirmation or walk-in issue, a locked daily counter increments the number for that service and date.

Thermal ticket printing. The 58mm printer issues a number-only ticket. Estimated waiting time may appear as one compact line when sufficient completed history exists. The student name is never printed.

Real-time queue display. The public monitor shows Now Serving and up to 10 waiting numbers in the same fair order used by Call Next. Names and held tickets are omitted.

Staff queue management. Authenticated staff can Call Next, Recall, Complete, Hold, and Call Held at their assigned window. Each window has an independent service timer.

Transaction recording. Queue records and queue calls support monitoring and done-only wait and service reports.

Student ID validation and privacy. Student ID is typed or tapped only on confirmation. Names are not printed, shown on the public display, or sent by SMS. The kiosk does not show the full student mobile number.

Authorized walk-in issuance. The kiosk corner control asks for the walk-in PIN on every use. The server checks the PIN. Several walk-in tickets may be open at once. Staff see Walk-in (reason). Print and display stay number-only. Forgetting the physical ID is not a walk-in.

RFID/NFC confirmation. An enrolled ISO 14443A / MIFARE card serial is read by the ACR122U-A9 and mapped to a Student ID. Unknown cards are rejected. Service and Regular or Priority are still selected first.

GSM SMS notices. After a successful Student ID lookup, **Notify me by SMS** is off by default. Turning it on copies the stored student mobile onto the ticket. Students do not type a number. If no mobile is on file, the control is disabled and shows **No SMS number on file**. Walk-in tickets may use a number entered only on the PIN overlay. An opted-in ticket may receive one local GSM text through the SIM7600G-H module when it is five waiting tickets away in fair call order (with an estimated wait when history is sufficient) and another text when the number is called or recalled. Call Next does not wait on the modem. The kiosk does not show the full mobile number.

Paper-out alert. When the XP-Q90EC printer has no paper, the staff dashboard shows an alert. The stored ticket remains valid and can be reprinted after the roll is replaced.

### 4.3 System Modules

The developed system consists of four major modules.

#### 4.3.1 Student Kiosk Module

The Student Kiosk Module is the public entry point. A student selects a service and Regular or Priority, then types a Student ID on the confirmation keypad or taps an enrolled RFID/NFC card. The system looks up the ID, may show the name for verification, may show waiting counts and estimated wait, and keeps Confirm disabled until lookup succeeds. **Notify me by SMS** is off by default and uses the stored student mobile; students do not type a number. After confirmation it stores the ticket and prints the 58mm number. A walk-in user uses the corner PIN control, selects service, priority, and reason, may enter a mobile number on the overlay, and receives the same number-only print. After print, a countdown returns to the kiosk. The student chrome remains a three-step flow.

#### 4.3.2 Staff Module

The Staff Module is used by authorized staff assigned to one window. Staff see the waiting list in fair call order, the held list, the current number, the student name or Walk-in (reason), and the window timer. They can Call Next, Recall, Complete, Hold, and Call Held. Hold finishes the open call, sets status to held, frees the window, and removes the number from Now Serving and waiting lists. Call Held resumes a held ticket at that window without 2 Priority → 1 Regular and requires no open serving ticket. Concurrent Call Next actions on shared services do not assign the same ticket twice. Staff history shows the user’s own done tickets. A paper-out alert appears on the staff dashboard when the thermal printer has no paper.

#### 4.3.3 Administrator Module

The Administrator Module provides live totals, waiting information, user management, student add/delete/CSV import, RFID/NFC card UID and mobile enrollment, kiosk PIN settings, walk-in issue, history, all tickets including held, and overall and per-window average waiting and service times. Reports use the latest completed call of a done ticket. Hold sessions are not counted as served. There is no public Services CRUD screen; office and window records are configured as part of the deployed domain data. The Guard role cannot log in.

#### 4.3.4 Queue Display Module

The Queue Display Module groups windows and shows Now Serving and waiting numbers only. Waiting rows use the same fair order as Call Next, with a maximum of 10 rows. Held tickets and student names are omitted. Voice announcement may be enabled in the browser.

### 4.4 Database Implementation

The system uses a MySQL/MariaDB database. Domain tables are services, windows, users, students, settings, daily_queue_counters, queues, and queue_calls.

**Table 4.1 Implemented Domain Tables**

| Table | Purpose |
| --- | --- |
| services | Service name and ticket prefix |
| windows | Counter name, display group, assigned service, and status |
| users | Admin, staff, and guard accounts; staff window assignment is unique |
| students | Authorized Student ID, name, optional RFID/NFC serial (UID), optional mobile |
| settings | Kiosk walk-in PIN (`walkin_pin`) |
| daily_queue_counters | Last number per service and date |
| queues | Ticket number, service, optional student or issuer/reason, optional notify mobile, priority, status, date |
| queue_calls | Window, called time, and finished time |

Queue status values used in operation are waiting, serving, held, done, and cancelled. Walk-in tickets leave `student_id` empty and store `issued_by` and `issue_reason`. The student name is never stored on the ticket row. Reports count done tickets only.

### 4.5 User Interface Presentation

The system provides separate interfaces by role.

The kiosk uses a compact three-step layout suitable for a 1024×600 screen: service, priority, and confirmation with summary, Student ID keypad, and RFID/NFC reader. A small corner control opens the PIN pad for walk-in issuance.

The staff panel shows the current ticket, name or Walk-in (reason), timer, fair waiting list, held list, Call Next, Recall, Complete, Hold, Call Held, and a paper-out alert when the thermal printer has no paper. An optional System Float window can stay on top on a staff PC.

The public display shows grouped windows, Now Serving, and waiting numbers only.

The administrator dashboard shows live totals, users, students, kiosk PIN, walk-in issue, history, tickets, and done-only reports.

Screenshots placed in this section are placeholders and must be replaced with captures of the current kiosk steps (including confirm keypad and RFID/NFC tap), number-only 58mm ticket, staff panel, public display, and administrator dashboard. The descriptions above match the implemented system. Diagrams may be inserted later.

### 4.6 System Implementation

The system was implemented with PHP 8.2 or higher, Laravel 12, Blade, Vite, Tailwind CSS, and MySQL/MariaDB on Windows/XAMPP on the mini PC (Intel Core i5 8th Generation, 8 GB RAM). That mini PC is both the kiosk host and the application server. Local assets are bundled so the interfaces do not depend on a CDN. The kiosk, XP-Q90EC printer, ACR122U-A9 reader, SIM7600G-H module, public display, staff PCs, administrator PC, and database communicate on the LAN [11].

The student path is service → priority → type or tap Student ID confirm → optionally turn on SMS (stored mobile) → generate → print → wait on the display → staff call → serve or hold → complete. Optional GSM SMS is queued at five tickets away and when called. The walk-in path substitutes PIN unlock, reason, and optional overlay mobile for Student ID. FairQueueScheduler applies 2 Priority → 1 Regular on shared services. WaitTimeEstimator supplies an estimate only when enough completed samples exist. StudentQueueGuard enforces the one-open-student-ticket-per-day rule. KioskWalkInGate checks the PIN on the server.

Staff actions update `queue_calls` and queue status. Hold is not Complete. Administrators maintain users, students, and the walk-in PIN, and review done-only reports. The implementation therefore matches the standalone localized deployment described for campus information systems [9].

### 4.7 Testing Results

#### 4.7.1 Functional Testing

Functional testing checked whether the major functions met the documented requirements.

**Table 4.2 Functional Testing Results**

| Function tested | Expected result | Result |
| --- | --- | --- |
| Public service selection | Cashier, Registrar, and DMO appear; Promissory Notes is hidden | Passed |
| Priority selection | Regular or Priority is stored | Passed |
| Student ID confirmation | Unknown ID or unknown card is rejected; Confirm stays disabled until lookup succeeds; keypad remains available without a card | Passed |
| One open student ticket | A second open ticket for the same ID on the same day is rejected | Passed |
| Walk-in PIN | Wrong PIN is rejected; correct PIN allows issue without Student ID | Passed |
| RFID/NFC tap | Enrolled card on the ACR122U-A9 fills confirm; unenrolled card is rejected; service and priority were already selected | Passed |
| Queue generation and print | A unique daily number is stored and printed without a name | Passed |
| Paper-out alert | Staff dashboard shows an alert when the thermal printer has no paper; stored ticket remains valid | Passed |
| Fair Call Next | 2 Priority → 1 Regular; two cashier windows do not claim the same ticket | Passed |
| GSM SMS | Five-ahead and called texts go only to tickets that opted in (stored student mobile, or walk-in overlay number); name omitted; queue still runs if the SIM7600G-H modem is unplugged; Confirm still prints when no number is on file | Passed |
| Hold and Call Held | Hold removes the number from Now Serving; Call Held resumes it; hold is not done | Passed |
| Public display | Serving and waiting numbers only; held tickets and names omitted | Passed |
| Administrator functions | Users, students, kiosk PIN, walk-in issue, history, and done-only reports | Passed |

The tested functions therefore covered the kiosk, Student ID keypad and ACR122U-A9 RFID/NFC tap, walk-in rules, printing, paper-out alert, optional GSM SMS through the SIM7600G-H module, fair scheduling, staff controls, display privacy, and administration.

#### 4.7.2 Non-Functional Testing

Non-functional testing assessed usability, efficiency, reliability and accuracy, security, compatibility, and local-network operation.

The system processed kiosk requests, printed tickets, updated staff and display views, and recorded calls during testing on the LAN. Interfaces remained a three-step student path and a role-based panel for staff and administrators. The walk-in PIN was checked on the server. The Guard role could not log in. No percentage reduction in waiting time and no fixed response-time benchmark are claimed, because the study does not report a controlled before-and-after operational measurement.

#### 4.7.3 User Acceptance and Expert Evaluation

The Chapter 3 student-and-parent questionnaire measured the current line and the proposed kiosk before development. The results in this section are evaluation results of the implemented system. They are not mixed with Tables 3.1–3.3.

Evaluation after testing used five instruments. All used the same five-point scale: 5 Strongly Agree, 4 Agree, 3 Neutral, 2 Disagree, and 1 Strongly Disagree. Weighted means used the same formula and interpretation ranges as Chapter 3.

The Technical Expert System Evaluation Form was completed by one IT expert who tested the system. Expert ratings are not combined with the user means. User Acceptance Testing used Cashier, Registrar, and DMO staff forms (N = 1 each) and the Student System Feedback Form (N = 30).

The five instruments and their scored sections are:

| Instrument | Respondents | Sections scored | Items |
| --- | --- | --- | --- |
| Technical Expert System Evaluation Form | 1 IT expert | Functionality; Usability; Efficiency; Reliability and Accuracy; User Satisfaction | 25 |
| Cashier System Feedback Form | Cashier staff (N = 1) | Functionality; Queue Management and Multi-Counter Operation; Usability and Efficiency; Reliability and Accuracy; Overall Satisfaction | 25 |
| Registrar System Feedback Form | Registrar staff (N = 1) | Functionality; Queue Management; Usability and Efficiency; Reliability and Accuracy; Overall Satisfaction | 25 |
| DMO System Feedback Form | DMO staff (N = 1) | Functionality; Queue Management; Usability and Efficiency; Reliability and Accuracy; Overall Satisfaction | 25 |
| Student System Feedback Form | Students, parents, and walk-in visitors (N = 30) | Functionality; Usability; Efficiency; Reliability and Accuracy; User Satisfaction | 27 |

The Student System Feedback Form was administered to **20 students**, **5 parents**, and **5 walk-in visitors** at PECIT (N = 30). Student section means are reported in Table 4.9. The IT expert results are reported in Table 4.4.

##### 4.7.3.1 Combined User Acceptance Results

**Table 4.3 User Acceptance Evaluation (office staff and student form; IT expert in Table 4.4 only)**

| Evaluation Area | Mean | Interpretation |
| --- | --- | --- |
| Functionality | 4.93 | Strongly Agree |
| Usability | 4.98 | Strongly Agree |
| Efficiency | 4.97 | Strongly Agree |
| Reliability and Accuracy | 4.93 | Strongly Agree |
| User Satisfaction | 4.99 | Strongly Agree |
| Overall Mean | 4.96 | Strongly Agree |

Table 4.3 combines Cashier, Registrar, DMO, and Student System Feedback Form means. Functionality uses office Section A and student Section A; Usability uses office Section C and student Section B; Efficiency uses office Section C and student Section C; Reliability and Accuracy uses office Section D and student Section D; User Satisfaction uses office Section E and student Section E. The IT expert score is kept in Table 4.4 only. Efficiency is a perception score, not a measured cut in waiting time.

##### 4.7.3.2 Technical Expert Evaluation

**Table 4.4 Technical Expert System Evaluation (N = 1)**

| Evaluation Area | Items | Mean | Interpretation |
| --- | --- | --- | --- |
| Functionality | 5 | 5.00 | Strongly Agree |
| Usability | 5 | 5.00 | Strongly Agree |
| Efficiency | 5 | 5.00 | Strongly Agree |
| Reliability and Accuracy | 5 | 5.00 | Strongly Agree |
| User Satisfaction | 5 | 5.00 | Strongly Agree |
| Overall Mean | 25 | 5.00 | Strongly Agree |

All 25 IT expert items were rated 5 (Strongly Agree), for section means of 5.00 and an overall mean of 5.00, Strongly Agree. The expert stated that the system is ready and can be deployed in PECIT, that network configuration must be set up because PECIT uses a cloud-based network, and that the UI design should be modernized. The expert score is not mixed into Table 4.3.

##### 4.7.3.3 Results by Feedback Form

**Table 4.5 Overall Means by Feedback Form**

| Respondent group | Instrument | Items | N | Overall mean | Interpretation |
| --- | --- | --- | --- | --- | --- |
| Cashier staff | Cashier System Feedback Form | 25 | 1 | 5.00 | Strongly Agree |
| Registrar staff | Registrar System Feedback Form | 25 | 1 | 5.00 | Strongly Agree |
| DMO staff | DMO System Feedback Form | 25 | 1 | 4.92 | Strongly Agree |
| Students, parents, and walk-in visitors | Student System Feedback Form | 27 | 30 | 4.91 | Strongly Agree |

**Table 4.6 Cashier System Feedback Form — Section Means (N = 1)**

| Section | Items | Mean | Interpretation |
| --- | --- | --- | --- |
| A. Functionality | 5 | 5.00 | Strongly Agree |
| B. Queue Management and Multi-Counter Operation | 5 | 5.00 | Strongly Agree |
| C. Usability and Efficiency | 5 | 5.00 | Strongly Agree |
| D. Reliability and Accuracy | 5 | 5.00 | Strongly Agree |
| E. Overall Satisfaction | 5 | 5.00 | Strongly Agree |
| Overall Mean | 25 | 5.00 | Strongly Agree |

All 25 Cashier items were rated 5 (Strongly Agree). The Cashier evaluator liked that the queuing system organizes students and parents efficiently and makes the cashier process more orderly. The main difficulty noted was getting familiar with the system at first. No further improvement was suggested.

**Table 4.7 Registrar System Feedback Form — Section Means (N = 1)**

| Section | Items | Mean | Interpretation |
| --- | --- | --- | --- |
| A. Functionality | 5 | 5.00 | Strongly Agree |
| B. Queue Management | 5 | 5.00 | Strongly Agree |
| C. Usability and Efficiency | 5 | 5.00 | Strongly Agree |
| D. Reliability and Accuracy | 5 | 5.00 | Strongly Agree |
| E. Overall Satisfaction | 5 | 5.00 | Strongly Agree |
| Overall Mean | 25 | 5.00 | Strongly Agree |

All 25 Registrar items were rated 5 (Strongly Agree). The Registrar evaluator stated that the system is very easy to use. No problems or further suggestions were reported on the form.

**Table 4.8 DMO System Feedback Form — Section Means (N = 1)**

| Section | Items | Mean | Interpretation |
| --- | --- | --- | --- |
| A. Functionality | 5 | 4.80 | Strongly Agree |
| B. Queue Management | 5 | 5.00 | Strongly Agree |
| C. Usability and Efficiency | 5 | 5.00 | Strongly Agree |
| D. Reliability and Accuracy | 5 | 4.80 | Strongly Agree |
| E. Overall Satisfaction | 5 | 5.00 | Strongly Agree |
| Overall Mean | 25 | 4.92 | Strongly Agree |

In Sections A and D, four items were rated Strongly Agree (5) and one item Agree (4), for a section mean of 4.80. Sections B, C, and E were all Strongly Agree (5.00). The overall mean is 4.92, Strongly Agree. The DMO evaluator liked the system, reported no problems so far, and suggested considering branding or logo placement and optional dark mode for the staff interface.

**Table 4.9 Student System Feedback Form — Section Means (N = 30)**

| Section | Items | Mean | Interpretation |
| --- | --- | --- | --- |
| A. Functionality | 6 | 4.90 | Strongly Agree |
| B. Usability | 6 | 4.93 | Strongly Agree |
| C. Efficiency | 5 | 4.88 | Strongly Agree |
| D. Reliability and Accuracy | 5 | 4.90 | Strongly Agree |
| E. User Satisfaction | 5 | 4.95 | Strongly Agree |
| Overall Mean | 27 | 4.91 | Strongly Agree |

All student-form section means are Strongly Agree. Functionality was 4.90, Usability 4.93, Efficiency 4.88, Reliability and Accuracy 4.90, and User Satisfaction 4.95, for an overall mean of 4.91. Respondents were 20 students, 5 parents, and 5 walk-in visitors (N = 30). Comments were positive: clients found the kiosk steps clear, the printed ticket easy to keep, and the public display helpful while waiting.

#### 4.7.4 Functionality Evaluation

Office Functionality means were 5.00 (Cashier), 5.00 (Registrar), and 4.80 (DMO). The student Functionality mean was 4.90. The combined Functionality mean in Table 4.3 is 4.93, Strongly Agree. This covers service selection, confirmation, ticket issuance, fair calling, display, and recording as judged by staff and clients.

#### 4.7.5 Usability Evaluation

Office Usability and Efficiency sections were all 5.00. The student Usability mean was 4.93. The combined Usability mean in Table 4.3 is 4.98, Strongly Agree. Staff and clients found the interfaces easy to understand; the Registrar evaluator specifically said the system is very easy to use, and the Cashier evaluator noted only initial familiarization as a difficulty.

#### 4.7.6 Efficiency Evaluation

The combined Efficiency mean in Table 4.3 is 4.97, Strongly Agree, from office Usability and Efficiency sections and the student Efficiency section (4.88). The Cashier evaluator perceived more orderly processing of students and parents. Efficiency is a perception score and is not a measured reduction in waiting time.

#### 4.7.7 Reliability and Accuracy Evaluation

Office Reliability and Accuracy means were 5.00 (Cashier), 5.00 (Registrar), and 4.80 (DMO). The student mean was 4.90. The combined mean in Table 4.3 is 4.93, Strongly Agree.

#### 4.7.8 User Satisfaction Evaluation

Office Overall Satisfaction sections were all 5.00. The student User Satisfaction mean was 4.95. The combined User Satisfaction mean in Table 4.3 is 4.99, Strongly Agree. Staff and clients reported that they liked the system. DMO suggested branding or logo and optional dark mode; the IT expert recommended modernizing the UI and preparing network configuration for PECIT's cloud-based network.

#### 4.7.9 ISO/IEC 25010-Aligned Evaluation

The evaluation forms used ISO/IEC 25010 product-quality characteristics as the basis for the rating items [10]. Ariningsih and Muhammad [2] likewise used ISO/IEC 25010:2023 with a Likert-scale questionnaire to evaluate a campus ticketing application. The IT expert form and the student form used five areas: Functionality, Usability, Efficiency, Reliability and Accuracy, and User Satisfaction. The Cashier, Registrar, and DMO forms used office-specific sections, including Queue Management, and combined Usability and Efficiency. Office and student scores are mapped to the five ISO-aligned areas in Table 4.3. The combined user overall mean is **4.96**, **Strongly Agree**. The IT expert overall mean in Table 4.4 is **5.00**, **Strongly Agree**, and is not mixed into Table 4.3. The study reports an ISO/IEC 25010-based evaluation form, not a certification of complete standard compliance.

### 4.8 Discussion of Findings

The findings are discussed against the five specific objectives.

Objective 1. The kiosk, shared service queues, and staff windows organized Cashier, Registrar, and DMO transactions that previously depended on a physical first-come-first-served line.

Objective 2. Confirmation required a validated Student ID, typed or tapped, or an authorized walk-in PIN path before a number was issued. Unknown IDs, unknown cards, and a second open student ticket on the same day were rejected.

Objective 3. Each confirmed request produced a 58mm number-only ticket. If the printer was out of paper, the staff dashboard showed a paper-out alert and the stored ticket remained valid. Call Next applied 2 Priority → 1 Regular across shared windows, including Cashier 1–3, without assigning the same ticket twice.

Objective 4. The public display and staff panel showed current serving and waiting numbers in the same fair order. Held tickets and names were omitted from the public view. Optional local GSM SMS through the SIM7600G-H module notified an opted-in ticket (stored student mobile, or walk-in overlay number) when the ticket was five places away and when it was called.

Objective 5. Functional tests passed for the implemented rules. Cashier and Registrar scored **5.00** overall, DMO scored **4.92**, and the Student System Feedback Form scored **4.91** overall, all **Strongly Agree**. The combined user mean in Table 4.3 is **4.96**, **Strongly Agree**. The IT expert scored **5.00** overall in Table 4.4 and recommended deployment readiness, network setup for PECIT's cloud-based network, and UI modernization.

Taken together, the implementation and evaluation show that the standalone kiosk system can replace the manual line with an organized, monitored, and recorded queue process at the selected PECIT offices. The study does not claim a measured percentage reduction in waiting time.
