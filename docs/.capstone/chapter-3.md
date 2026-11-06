# CHAPTER 3

## RESEARCH METHODOLOGY

This chapter presents the methods and procedures used in the development of the Standalone Kiosk-Based Queuing Management System with Thermal Printer for the student service offices of the Philippine Electronics and Communication Institute of Technology (PECIT). It discusses the conceptual framework, requirements analysis, requirements documentation, system analysis and design, operational environment, interfaces, development methodology, system workflow, project management plan, verification and testing plans, and ethical considerations. The methodology was structured so that the developed system would address the identified problems and satisfy the requirements of its intended users.

The current PECIT process is manual first-come-first-served lining at the Cashier, Registrar, and Data Management Office (DMO). The methods in this chapter were therefore directed toward replacing that physical line with a standalone, LAN-based kiosk process on one mini PC (Intel Core i5 8th Generation, 8 GB RAM) that confirms a Student ID by keypad or ACR122U-A9 RFID/NFC tap, or an authorized walk-in request, prints a 58mm thermal ticket, alerts staff when the printer is out of paper, may send local GSM SMS notices through a SIM7600G-H module, applies fair multi-window scheduling, and provides real-time monitoring and administrative reports.

### 3.1 Conceptual Framework

The study uses the Input–Process–Output (IPO) Framework to show how hardware, software, and system requirements are transformed through the Waterfall Software Development Model into the completed queue management system [19].

*Figure 3.1. Input–Process–Output (IPO) Framework*

#### 3.1.1 Input

The input stage consists of the hardware, software, and system requirements needed to develop and operate the proposed system.

##### 3.1.1.1 Hardware Requirements

The hardware requirements include one mini PC that serves as both the public kiosk host and the application server (Intel Core i5 8th Generation, 8 GB RAM), a touchscreen monitor, a 58mm USB thermal printer (XP-58 / XP-Q90EC), a USB RFID/NFC reader (ACR122U-A9, ISO 14443A / MIFARE), a GSM/LTE USB module (SIM7600G-H) on the same mini PC, a public HDMI display, an AVR HDMI box, staff workstations, an administrator computer, keyboard and mouse, a LAN router or network switch, and the USB, HDMI, AVR, Ethernet, and power cables required for local deployment at PECIT.

##### 3.1.1.2 Software Requirements

The software requirements include Windows 10/11, PHP 8.2 or higher, Laravel 12, Blade, Vite, Tailwind CSS, MySQL/MariaDB, XAMPP, Visual Studio Code, Composer, Node.js/npm, Google Chrome or Microsoft Edge, and Git. Laravel is the application framework. MySQL/MariaDB stores local queue data. XAMPP provides the local server environment. Essential queue operations do not require continuous internet connectivity [11].

##### 3.1.1.3 System Requirements

The system requirements include service selection; Regular or Priority selection; confirmation before ticket generation; Student ID validation by keypad or RFID/NFC tap or authorized kiosk-PIN walk-in issuance; one open student ticket per Student ID per day; queue-number generation; 58mm thermal printing; estimated waiting time when sufficient completed history is available; optional local GSM SMS when a student turns on **Notify me by SMS** (off by default; stored student mobile) or when a walk-in enters a number on the PIN overlay, sent when a ticket is five places away and when it is called; shared service queues; 2 Priority → 1 Regular scheduling; multi-counter support; staff Call Next, Recall, Complete, Hold, and Call Held; number-only public display; transaction recording; administrator user, student, card UID, mobile, PIN, and report functions; authentication; and standalone LAN operation.

The student kiosk remains a three-step flow: service selection, priority selection, and confirmation. During confirmation, the Student ID is typed or read from an enrolled RFID/NFC card and validated against the authorized student list. Clients who do not bring the card may still type the Student ID. Clients who cannot use a Student ID may request a walk-in ticket at the same kiosk after the walk-in PIN is accepted. Cashier 1, Cashier 2, and Cashier 3 share one Cashier waiting queue while keeping independent active transactions and service timers.

#### 3.1.2 Process

*Figure 3.2. Waterfall Software Development Model Used in the Study*

The process stage uses five sequential Waterfall phases.

##### 3.1.2.1 Requirements Analysis

This phase identified the problems of the existing first-come-first-served line and determined the functional, non-functional, and operational requirements of students, parents, Cashier, Registrar, and DMO personnel, and the administrator. Questionnaires, interviews, observation, and document review were used.

##### 3.1.2.2 System Design

This phase converted the requirements into the architecture, process models, database structure, workflow, and interface designs. The design covers the mini PC (kiosk and server), 58mm printer, ACR122U-A9 RFID/NFC reader, SIM7600G-H GSM/LTE module, staff panels, public display, and administrator dashboard, including walk-in PIN issuance, fair scheduling, Hold and Call Held, paper-out alerts on the staff dashboard, optional SMS notices, and privacy rules.

##### 3.1.2.3 Implementation/Development

This phase produced the working application: Student Kiosk Module, Staff Module, Administrator Module, Queue Display Module, local database, thermal printer integration, RFID/NFC confirmation, and GSM SMS sending. Implementation includes Student ID validation by keypad or tap, kiosk-PIN walk-in issuance, queue generation, thermal printing, shared-queue processing, priority scheduling, staff controls, and transaction recording.

##### 3.1.2.4 Testing

Testing verified the kiosk, Student ID keypad and RFID/NFC tap, walk-in rules, one-open-student-ticket-per-day rule, queue generation, 58mm printing, paper-out alert on the staff dashboard, GSM SMS five-ahead and called notices, fair scheduling, staff functions, public display, multi-counter behavior, database transactions, and access controls. Integration testing verified communication among the mini PC (kiosk and server), database, printer, ACR122U-A9 reader, SIM7600G-H module, staff module, display, and administrator module.

##### 3.1.2.5 Deployment

Deployment installed and configured the mini PC as both kiosk and server, the XP-Q90EC printer, ACR122U-A9 reader, SIM7600G-H module, public display, staff workstations, administrator computer, local database, and LAN components in the selected PECIT offices. Essential queue operations run without continuous internet connectivity.

#### 3.1.3 Output

The output is the completed standalone LAN-based queue management system for the selected PECIT student service offices.

##### 3.1.3.1 Standalone Kiosk-Based Queuing System

The kiosk is the primary client interface. Students select a service, choose Regular or Priority, type or tap and validate a Student ID, confirm the request, and receive a queue number. Authorized walk-in tickets are issued at the same kiosk after the walk-in PIN is accepted.

##### 3.1.3.2 Printed Queue Ticket

After confirmation, the system prints a 58mm thermal ticket with the queue number and necessary queue information. Estimated waiting time may appear when enough completed history exists. The student name is not printed.

##### 3.1.3.3 Real-Time Queue Display

The public display shows current serving numbers and waiting queues in the same fair order used by Call Next. Student names and held tickets are omitted.

##### 3.1.3.4 Staff Queue Management

Authorized staff can Call Next, Recall, Complete, Hold, and Call Held. Windows that share a service use one waiting queue and keep independent active calls and timers. Staff may see the student name while serving a validated student ticket, or Walk-in (reason) for an authorized walk-in ticket.

##### 3.1.3.5 Queue Records and Administrative Reporting

The local database stores services, windows, users, students, settings, daily counters, queues, and queue calls. Administrators can manage users and student records, set the kiosk walk-in PIN, issue walk-in tickets, review history, and generate waiting-time and service-time reports from completed transactions only.

##### 3.1.3.6 Organized and Fair Queue Management

The system replaces physical first-come-first-served lining with automated numbering, 2 Priority → 1 Regular scheduling, shared multi-counter queues, real-time monitoring, staff controls, and recorded completed-service reports.

#### 3.1.4 Explanation of the IPO Framework

Hardware, software, and system requirements are the inputs. The five Waterfall phases transform those inputs into a localized standalone system for PECIT [9]. The completed system integrates the kiosk, Student ID validation by keypad or RFID/NFC tap, kiosk-PIN walk-in issuance, 58mm printing, estimated waiting time when data are sufficient, optional local GSM SMS, Regular and Priority queueing, 2 Priority → 1 Regular scheduling, Hold and Call Held, a number-only public display, and done-only administrative reports.

### 3.2 Requirements Analysis

Requirements analysis identified the problems of the existing first-come-first-served line and the functions needed by the proposed system. Students and parents answered a questionnaire. Cashier, Registrar, and DMO personnel were interviewed. Observation and document review were used to understand the actual office process.

#### 3.2.1 Stakeholder Identification

The stakeholders were students and parents, Cashier, Registrar, and DMO personnel, and the system administrator.

Students and parents are the primary clients because they join the current physical line and will use the kiosk and public display.

Cashier, Registrar, and DMO personnel process transactions and need structured queue controls.

The administrator manages authorized users and student records, sets the kiosk walk-in PIN, monitors queues, and generates reports. The Guard role exists only as the kiosk issuer record and cannot log in.

#### 3.2.2 Data Gathering Methods

Questionnaire. A questionnaire (needs-assessment survey) was administered to students and parents about their experience with the current Cashier, Registrar, and DMO lining process and their perception of a kiosk-based alternative. This form is mixed: Part I collected respondent information; Part II used a five-point Likert scale (quantitative); Part III used checklist items with an optional Other line; Part IV collected open suggestions (qualitative). The printed form labels Part III as a “Student Interview Guide,” but students and parents were not interviewed. Those items were answered on the same questionnaire. Interview was used only with Cashier, Registrar, and DMO staff. Questionnaire results belong in Chapter 3 (Tables 3.1–3.3). They are not evaluation scores of the finished system.

**Where survey and evaluation results belong**

| Result | Instrument | When gathered | Where reported |
| --- | --- | --- | --- |
| Needs-assessment survey | Student-and-parent questionnaire (Parts I–IV) | Before development | Chapter 3, Tables 3.1–3.3 |
| Staff interview | Oral interview with Cashier, Registrar, and DMO personnel | Before development | Chapter 3, Section 3.2 |
| Functional and non-functional tests | Test cases on the implemented LAN system | After implementation | Chapter 4, Sections 4.7.1–4.7.2 |
| Expert evaluation | Technical Expert System Evaluation Form | After implementation | Chapter 4, Table 4.4 |
| User acceptance | Cashier, Registrar, DMO, and Student System Feedback Forms | After implementation | Chapter 4, Tables 4.3 and 4.5–4.9 |

**Respondent profile**

| Respondent | Frequency | Percentage |
| --- | --- | --- |
| Students | 40 | 80% |
| Parents | 10 | 20% |
| Total | 50 | 100% |

Fifty respondents completed the form: 40 students (80%) and 10 parents (20%).

| Rating | Interpretation |
| --- | --- |
| 5 | Strongly Agree |
| 4 | Agree |
| 3 | Neutral |
| 2 | Disagree |
| 1 | Strongly Disagree |

Interview. Interviews with Cashier, Registrar, and DMO personnel covered existing first-come-first-served procedures, operational difficulties, priority handling, and needed staff functions.

Observation. The researchers observed client movement, line formation, waiting, and staff calling practices.

Document Review. Existing procedures and related records were reviewed to determine the information needed for ticket generation, staff serving, and reporting.

#### 3.2.3 Existing System Analysis

The current process is a manual first-come-first-served line that uses no queue ticket. Likert-scale results from the 50 respondents were analyzed using the weighted mean:

WM = Σfx / N

where Σfx is the sum of the products of frequency and rating, and N is the number of responses.

| Weighted Mean Range | Interpretation |
| --- | --- |
| 4.21–5.00 | Strongly Agree |
| 3.41–4.20 | Agree |
| 2.61–3.40 | Neutral |
| 1.81–2.60 | Disagree |
| 1.00–1.80 | Strongly Disagree |

**Table 3.1 Current Queuing Experience**

| Questionnaire Statement | Weighted Mean | Interpretation |
| --- | --- | --- |
| 1. The current queuing system is well organized. | 2.43 | Disagree |
| 2. The waiting time before being served is reasonable. | 2.30 | Disagree |
| 3. I experience confusion during the queuing process. | 4.17 | Agree |
| 4. The current queuing system meets my needs as a student. | 2.47 | Disagree |
| 5. The current queuing process needs improvement. | 4.40 | Strongly Agree |
| Overall Mean | 3.15 | Neutral |

The overall mean of 3.15 is Neutral, but the item means show why the study was conducted. Respondents strongly agreed that the current process needs improvement (4.40) and agreed that they experience confusion (4.17). They disagreed that the line is well organized (2.43), that waiting time is reasonable (2.30), and that the process meets their needs (2.47).

Part III of the student-and-parent questionnaire (checklist items, not an interview) was summarized by the most checked box on each item:

**Table 3.2 Most Checked Responses from the Student and Parent Questionnaire (Part III)**

| Item | Most checked response |
| --- | --- |
| 1. Experience in line | The lines are usually long, especially during peak hours such as enrollment and payment. |
| 2. Problems encountered | Some people cut in line or do not follow proper order. |
| 3. How a kiosk could help | It can organize the queue and prevent people from cutting in line. |
| 4. Printed ticket or display | Yes, because I can monitor my turn through the display screen. |
| 5. Recommended improvement | Installation of a kiosk for automatic queue number generation. |

These most-checked answers match the problem and the proposed system: the current first-come-first-served line is long, uses no ticket, and is subject to line cutting, and respondents want a numbered kiosk queue with a public display.

These findings support Specific Problems 1 and 4: the current first-come-first-served line uses no ticket, is subject to line cutting, and has no real-time display of who is being served and who is waiting.

#### 3.2.4 Requirements Gathering

**Table 3.3 Perception of the Proposed Kiosk-Based Queuing System**

| Questionnaire Statement | Weighted Mean | Interpretation |
| --- | --- | --- |
| 1. A kiosk-based automated queuing system would improve service efficiency. | 4.53 | Strongly Agree |
| 2. A printed queue ticket would help me track my turn easily. | 4.60 | Strongly Agree |
| 3. A real-time queue display would make the system more organized. | 4.57 | Strongly Agree |
| 4. Selecting services (Cashier, Registrar, DMO) through a kiosk would be convenient. | 4.50 | Strongly Agree |
| 5. A priority queue option (regular/priority) would improve service flexibility. | 4.37 | Strongly Agree |
| 6. A confirmation step before ticket generation would help reduce errors. | 4.43 | Strongly Agree |
| 7. I am willing to use a kiosk-based queuing system. | 4.63 | Strongly Agree |
| Overall Mean | 4.52 | Strongly Agree |

All seven proposed-system items were interpreted as Strongly Agree. Respondents were willing to use a kiosk (4.63). They also strongly agreed that a printed queue ticket (4.60), a real-time display (4.57), kiosk service selection (4.50), a confirmation step (4.43), and a Regular/Priority option (4.37) would improve the process. The overall mean of 4.52 is Strongly Agree. These ratings support the features built into the standalone kiosk system.

Part IV suggestions matched those features: install a kiosk that issues a queue number; print a ticket as a clear queue reference so clients need not remain in a physical line; show Now Serving and waiting numbers on a public display; let clients select Cashier, Registrar, or DMO at the kiosk; provide Regular and Priority; and confirm the request before the ticket is printed. SMS alerts requested in the needs assessment were implemented through a local GSM/LTE module rather than a cloud API. Suggestions such as extra counters only at peak hours remain later work.

**Table 3.3 Requirements Derived from the Data**

| Finding | Corresponding System Requirement |
| --- | --- |
| Need for better queue organization | Automated queue generation instead of a physical first-come-first-served line |
| Waiting-time concerns | Organized queue flow, ETA when history is sufficient, and queue monitoring |
| Confusion during queuing | Queue numbers, printed tickets, and a public display |
| Long lines and overcrowding | Kiosk-based issuance so clients need not remain in a physical line |
| Need for a clear queue reference | Printed 58mm thermal ticket, number only |
| Need to monitor the queue while waiting | Real-time public display without names or held tickets |
| Need for service selection | Cashier, Registrar, and DMO on the public kiosk |
| Need for priority handling | Regular/Priority selection and 2 Priority → 1 Regular calling |
| Concern about incorrect selections | Confirmation, with Student ID typed or tapped, or authorized walk-in issuance |
| Staff need better queue control | Call Next, Recall, Complete, Hold, and Call Held |
| Need for transaction monitoring | Queue records and done-only reports |
| Need to be notified while waiting | Optional local GSM SMS after **Notify me by SMS** (stored student mobile; walk-in overlay number) at five tickets away and when called |

Technical requirements of the implemented system were added to these user findings: Student ID validation by keypad or RFID/NFC tap, one open student ticket per day, kiosk-PIN walk-in issuance, 58mm printing, optional local GSM SMS, multi-counter Cashier queues, independent window timers, Hold and Call Held, number-only public display, settings-based walk-in PIN, and administrator reports based on completed transactions only.

### 3.3 Requirements Documentation

#### 3.3.1 Functional Requirements

The system shall:

1. Allow clients to select an available public service through the touchscreen kiosk. Promissory Notes is hidden from the public kiosk list.
2. Allow clients to select Regular or Priority.
3. Display a confirmation page before a ticket is generated.
4. Require a valid Student ID during student confirmation, entered on the keypad or read from an enrolled RFID/NFC card, and validate it against the authorized student list.
5. Reject a second open student ticket for the same Student ID on the same day.
6. Allow authorized walk-in issuance at the kiosk after the walk-in PIN is accepted, or by an administrator, without a Student ID.
7. Generate a unique queue number by service and operating date.
8. Store the queue record, including optional `student_id` or `issued_by` / `issue_reason` for walk-in tickets.
9. Print a 58mm thermal ticket that does not include the student name.
10. Show estimated waiting time only when sufficient completed history is available.
11. Place new tickets in the appropriate service queue.
12. Apply 2 Priority → 1 Regular when calling the next ticket.
13. Allow several windows assigned to the same service to share one waiting queue.
14. Allow staff to view the waiting list, Call Next, Recall, Complete, Hold, and Call Held at their assigned window.
15. Maintain an independent service timer for each window.
16. Display current serving and waiting numbers on the public display, omitting names and held tickets.
17. Optionally send a local GSM SMS when a waiting ticket that opted in is five places away in fair call order (with an estimated wait when history is sufficient) and when that number is called or recalled. Student opt-in uses **Notify me by SMS**, which is off by default and copies the mobile number stored on the student record. Students shall not type a mobile number on Confirm. If no mobile is on file, the control shall stay off and disabled and shall show **No SMS number on file**; Confirm and print shall still proceed. The kiosk shall not display the full mobile number; at most the last four digits may appear after opt-in. Walk-in tickets may use a number entered only on the PIN overlay. SMS shall not include the student name. Queue operations shall continue if the modem is unavailable.
18. Record queue calls and completion times.
19. Provide staff history of the user’s own completed tickets and administrator history and reports.
20. Allow administrators to manage users and student records, enroll RFID/NFC card serials (UID) and mobile numbers against Student IDs, set the kiosk walk-in PIN, issue walk-in tickets, and generate done-only wait and service reports.
21. Operate on the institution LAN without continuous internet connectivity.
22. Restrict staff and administrator functions to authenticated users. The Guard role cannot log in.
23. Detect when the 58mm thermal printer is out of paper and show an alert on the staff dashboard so windows can reload the printer. Stored tickets remain valid.

#### 3.3.2 Non-Functional Requirements

Performance. The system shall process queue requests, generate numbers, update displays, and communicate among local components within an acceptable response time during normal operation.

Reliability. The system shall consistently perform queue generation, printing, calling, hold and resume, completion, recording, and display updating during normal operation.

Usability. Interfaces shall be clear for clients, staff, and administrators. The student kiosk shall remain a three-step sequence.

Security. Only authenticated staff and administrators shall access protected functions. One staff user is assigned to a window. The walk-in PIN is checked on the server. The Guard role cannot log in.

Availability. The system shall remain available during the intended office hours.

Maintainability. The system shall support correction, database maintenance, updates, and later enhancement.

Scalability. Additional windows may share an existing service queue without changing the core scheduling structure.

Compatibility. The system shall operate with the supported Windows environment, local browser, mini PC kiosk-and-server host (Intel Core i5 8th Generation, 8 GB RAM), 58mm USB printer, ACR122U-A9 RFID/NFC reader, SIM7600G-H GSM/LTE USB module, HDMI display, MySQL/MariaDB, and LAN.

Privacy. The student name may appear on confirmation and to staff while serving. It shall not be printed, shown on the public display, or included in SMS. The kiosk shall not display the full student mobile number.

Local operation. Normal queue operations shall run on the LAN without continuous internet connectivity. Interface assets shall be local [11].

Evaluation quality. System testing, expert evaluation, and User Acceptance Testing shall use quality characteristics aligned with ISO/IEC 25010 [10].

#### 3.3.3 Business Rules

1. The current office process being replaced is a manual first-come-first-served line.
2. Student ID is typed or tapped only on the kiosk confirmation step and must match the authorized student list. An unknown card is treated as an unknown ID. Forgetting the card is not a walk-in.
3. Only Regular and Priority queue types are allowed.
4. A student ticket is generated only after confirmation of service, queue type, and a valid Student ID.
5. Only one open student ticket (`waiting`, `serving`, or `held`) is allowed per Student ID per day.
6. Walk-in tickets have no Student ID. They require the kiosk PIN or administrator issuance. Several walk-in tickets may be open at the same time.
7. The Guard role is the issuer record for kiosk walk-in tickets and cannot log in.
8. Queue numbers are generated by service and date through a locked daily counter.
9. Every generated ticket is printed on the configured 58mm printer. The name is never printed. If the printer is out of paper, the staff dashboard shows a paper-out alert; the stored ticket remains valid and can be reprinted after paper is loaded.
10. Call Next follows 2 Priority → 1 Regular across windows that share a service. Concurrent Call Next actions must not assign the same ticket twice.
11. Each window keeps its own open call and timer. A window cannot complete another window’s open call.
12. Hold finishes the open call, sets status to `held`, frees the window, and removes the number from Now Serving and waiting lists.
13. Call Held resumes a held ticket at the window without 2 Priority → 1 Regular and requires no open serving ticket.
14. The public waiting list uses the same fair order as Call Next and omits held tickets and names.
15. Reports count `done` tickets only. Hold sessions are not treated as completed service.
16. Estimated waiting time is omitted when completed-service history is insufficient (fewer than three completed samples).
17. One staff user is assigned to a service window.
18. Promissory Notes is hidden from the public kiosk service list.
19. Essential operations run on the LAN without continuous internet connectivity.
20. Optional GSM SMS is sent at most once for the five-tickets-away event and again when the ticket is called or recalled, and only when the ticket opted in. Position uses fair call order for that service. Held tickets are not counted as waiting.
21. RFID/NFC is an alternate confirm input only. Service and Regular or Priority are selected first. The reader is not used for payment or attendance.
22. Students do not type a mobile number on Confirm. **Notify me by SMS** is off by default. If `students.mobile` is empty, the control is disabled and shows **No SMS number on file**. The full number is not shown; last four digits may appear after opt-in. Walk-in clients may enter a number only on the PIN overlay.

#### 3.3.4 Use Case Descriptions

The primary actors are the Client, Staff, and Administrator. The system performs validation, queue generation, printing, scheduling, display updating, hold and resume handling, and reporting.

##### 3.3.4.1 Select Service

| Item | Description |
| --- | --- |
| Actor | Client |
| Objective | Select the required public service |
| Precondition | Kiosk is available |
| Normal Flow | 1. Client accesses the kiosk. 2. System displays Cashier, Registrar, and DMO. 3. Client selects a service. |
| Alternative Flow | If no valid service is selected, the system does not proceed. Promissory Notes is not shown. |
| Postcondition | Selected service is stored for the next step. |

##### 3.3.4.2 Select Queue Type

| Item | Description |
| --- | --- |
| Actor | Client |
| Objective | Select Regular or Priority |
| Precondition | Service has been selected |
| Normal Flow | 1. System displays Regular and Priority. 2. Client selects a type. |
| Alternative Flow | If no type is selected, the system does not proceed. |
| Postcondition | Queue type is stored. |

##### 3.3.4.3 Confirm Queue Request

| Item | Description |
| --- | --- |
| Actor | Client |
| Objective | Confirm the request after Student ID validation |
| Precondition | Service and queue type have been selected |
| Normal Flow | 1. Client types the Student ID on the keypad or taps an enrolled RFID/NFC card. 2. System looks up the Student ID (or the enrolled card serial). 3. Name may appear for verification. 4. Waiting counts and ETA may appear. 5. After lookup succeeds, **Notify me by SMS** is shown and is off by default. If a mobile number is on file, the client may turn it on; the kiosk does not show the full number (at most last four digits). If no mobile is on file, the control stays off and disabled and shows **No SMS number on file**. Students do not type a phone number. 6. Confirm remains disabled until lookup succeeds. 7. Client confirms. Print proceeds whether or not SMS is on. |
| Alternative Flow | Unknown ID, unknown card, or an already-open ticket today is rejected. The client may type the ID if they did not bring the card, return to a previous step, or use walk-in PIN only when they have no Student ID. |
| Postcondition | A valid student request is submitted for numbering. |

##### 3.3.4.4 Issue Walk-in Ticket

| Item | Description |
| --- | --- |
| Actor | Client / authorized issuer |
| Objective | Issue a ticket without a Student ID |
| Precondition | Kiosk walk-in control is used, or an administrator issues a walk-in ticket |
| Normal Flow | 1. Client or staff uses the kiosk corner control. 2. System asks for the walk-in PIN every time. 3. Server checks the PIN in settings. 4. Authorized user selects service, priority, and reason. 5. Because there is no student record, the overlay may accept an optional mobile number for SMS. If none is entered, no SMS is sent. 6. System issues the ticket. |
| Alternative Flow | Wrong PIN is rejected. After print or cancel, the next corner click asks for the PIN again. |
| Postcondition | A walk-in ticket is stored with `issued_by` and `issue_reason`. Print and display remain number-only. |

##### 3.3.4.5 Generate Queue Number

| Item | Description |
| --- | --- |
| Actor | System |
| Objective | Generate a unique queue number |
| Precondition | Student confirmation or authorized walk-in is valid |
| Normal Flow | 1. System locks the daily counter for the service and date. 2. System increments the counter. 3. System saves the queue record. |
| Alternative Flow | If validation fails, no number is generated. |
| Postcondition | Queue number and record exist. |

##### 3.3.4.6 Print Queue Ticket

| Item | Description |
| --- | --- |
| Actor | System |
| Objective | Print the 58mm ticket |
| Precondition | Queue record has been created |
| Normal Flow | 1. System prepares ticket data. 2. Browser/kiosk sends the print request. 3. XP-58 (XP-Q90EC) prints the number-only ticket. |
| Alternative Flow | If the printer is out of paper, the staff dashboard shows a paper-out alert. The stored ticket remains valid and can be reprinted after paper is loaded. |
| Postcondition | Client receives a physical queue reference. |

##### 3.3.4.7 View Queue Display

| Item | Description |
| --- | --- |
| Actor | Client |
| Objective | Monitor serving and waiting numbers |
| Precondition | Public display is operating |
| Normal Flow | 1. Display polls current serving and waiting data. 2. Waiting rows use fair call order, maximum 10. 3. Held tickets and names are omitted. |
| Postcondition | Clients see current queue information. |

##### 3.3.4.8 Call Next Queue

| Item | Description |
| --- | --- |
| Actor | Staff |
| Objective | Call the next fair-order ticket at this window |
| Precondition | Staff is logged in, assigned to a window, and has no open serving ticket |
| Normal Flow | 1. Staff selects Call Next. 2. System locks the service row. 3. FairQueueScheduler claims the next ticket. 4. Status becomes serving. 5. Window timer starts. |
| Alternative Flow | If no waiting ticket exists, the system reports that none is available. |
| Postcondition | The ticket is the window’s active transaction. |

##### 3.3.4.9 Recall Queue

| Item | Description |
| --- | --- |
| Actor | Staff |
| Objective | Re-announce the current or last called number |
| Precondition | A callable ticket exists at the window |
| Normal Flow | 1. Staff selects Recall. 2. System updates the recall token. 3. Display and voice announce the number again. |
| Postcondition | The number is re-announced. |

##### 3.3.4.10 Complete Transaction

| Item | Description |
| --- | --- |
| Actor | Staff |
| Objective | Finish the window’s open ticket |
| Precondition | The window has an open serving ticket |
| Normal Flow | 1. Staff selects Complete. 2. System records `finished_time`. 3. Queue status becomes `done`. 4. Display updates. |
| Alternative Flow | The system will not complete another window’s open call. |
| Postcondition | The ticket is counted as a completed transaction. |

##### 3.3.4.11 Hold Queue

| Item | Description |
| --- | --- |
| Actor | Staff |
| Objective | Set aside the open ticket and free the window |
| Precondition | The window has an open serving ticket |
| Normal Flow | 1. Staff selects Hold. 2. Open call is finished. 3. Status becomes `held`. 4. Number leaves Now Serving and waiting lists. |
| Postcondition | The window is free. The ticket is not treated as completed. |

##### 3.3.4.12 Call Held

| Item | Description |
| --- | --- |
| Actor | Staff |
| Objective | Resume a held ticket at this window |
| Precondition | No open serving ticket; a held ticket is selected |
| Normal Flow | 1. Staff selects Call on a held row. 2. System assigns the ticket to this window. 3. Status becomes serving. 4. Timer starts. |
| Alternative Flow | Call Held does not use 2 Priority → 1 Regular. |
| Postcondition | The held ticket is again the active transaction. |

##### 3.3.4.13 View Queue History

| Item | Description |
| --- | --- |
| Actor | Staff / Administrator |
| Objective | Review completed tickets |
| Precondition | User is authenticated |
| Normal Flow | Staff sees own `done` tickets. Administrator sees history, all tickets including held, and reports. |
| Postcondition | Authorized records are displayed. Hold sessions are not counted as served. |

##### 3.3.4.14 Manage Users

| Item | Description |
| --- | --- |
| Actor | Administrator |
| Objective | Manage admin and staff accounts |
| Precondition | Administrator is authenticated |
| Normal Flow | Administrator creates or updates users. Staff window assignment remains unique. Guard cannot be given login access. |
| Postcondition | User records are updated. |

##### 3.3.4.15 Manage Students

| Item | Description |
| --- | --- |
| Actor | Administrator |
| Objective | Maintain the Student ID allowlist |
| Precondition | Administrator is authenticated |
| Normal Flow | Administrator adds or deletes students or imports a CSV. Kiosk lookup uses this list. |
| Postcondition | Authorized Student IDs are current. |

##### 3.3.4.16 Set Kiosk PIN

| Item | Description |
| --- | --- |
| Actor | Administrator |
| Objective | Set the walk-in PIN stored in settings |
| Precondition | Administrator is authenticated |
| Normal Flow | Administrator updates `walkin_pin`. The PIN is not stored in the public kiosk page. |
| Postcondition | Later kiosk walk-in unlocks use the new PIN. |

##### 3.3.4.17 Generate Reports

| Item | Description |
| --- | --- |
| Actor | Administrator |
| Objective | View average waiting and service times |
| Precondition | Completed tickets exist |
| Normal Flow | System uses latest completed calls. Wait is created_at to called_time. Service is called_time to finished_time. |
| Postcondition | Overall and per-window averages are shown for `done` tickets only. |

##### 3.3.4.18 Estimate Waiting Time

| Item | Description |
| --- | --- |
| Actor | System |
| Objective | Estimate waiting time for the selected service |
| Precondition | Enough completed history and current waiting/serving data exist |
| Normal Flow | WaitTimeEstimator divides load by active windows. Estimate appears on confirm and may print as one line. |
| Alternative Flow | If fewer than three completed samples exist, the estimate is omitted. |
| Postcondition | Client sees an estimate only when data are sufficient. |

##### 3.3.4.19 Manage Multi-Counter Service Queue

| Item | Description |
| --- | --- |
| Actor | Staff / System |
| Objective | Let several windows serve one shared queue |
| Precondition | More than one window is assigned to the same service |
| Normal Flow | Cashier 1–3 share the Cashier waiting queue. Each keeps its own open call and timer. FairScheduler prevents duplicate claims. |
| Postcondition | Several counters serve one queue without assigning the same ticket twice. |

### 3.4 System Analysis and Design

#### 3.4.1 Business Process Analysis

Existing process. Students and other clients go directly to the Cashier, Registrar, or DMO and wait in a first-come-first-served line. Staff watch the line and call the next person by physical order. During busy periods this produces unclear order, line cutting, difficulty serving priority clients, overcrowding, and no shared view of who is waiting.

Proposed process. The client starts at the kiosk, selects service and Regular or Priority, and confirms with a typed or tapped Student ID or an authorized walk-in PIN path. The system stores the ticket and prints a 58mm number-only ticket. Optional GSM SMS is sent only after **Notify me by SMS** is turned on (stored student mobile) or after a walk-in number is entered on the PIN overlay, when the ticket is five places away and when it is called. Staff call by 2 Priority → 1 Regular across shared windows, and may Hold or Call Held. The public display shows serving and waiting numbers only. The administrator monitors completed-transaction reports.

#### 3.4.2 System Architecture

The system is standalone and LAN-based [11]. One mini PC (Intel Core i5 8th Generation, 8 GB RAM) serves as both the public kiosk host and the application server. Staff PCs, the public display, and the administrator computer connect to that mini PC over the LAN.

Mini PC (kiosk and server). Runs Windows, XAMPP/Laravel, and the kiosk browser. The touchscreen is the public client interface. USB ports hold the 58mm printer, ACR122U-A9 reader, and SIM7600G-H GSM module.

Touchscreen monitor. Public three-step student flow, RFID/NFC tap or keypad on confirm, and corner PIN walk-in issuance.

58mm USB thermal printer. XP-58 (XP-Q90EC). Prints the number and optional ETA line. No name. Paper-out is reported to the staff dashboard.

RFID/NFC reader. ACR122U-A9 USB reader at the kiosk; enrolled ISO 14443A / MIFARE card serial maps to Student ID.

GSM/LTE USB module. SIM7600G-H on the mini PC; local SMS, not a cloud SMS API.

LAN. Connects the mini PC, staff PCs, public display, and administrator PC.

Application server. Laravel 12 on the same mini PC, with Blade, Vite/Tailwind, and services for fair scheduling, wait estimation, student-ticket guarding, walk-in PIN checks, paper-out alerts, and optional SMS dispatch.

MySQL/MariaDB. Stores services, windows, users, students, settings, daily_queue_counters, queues, and queue_calls.

Staff windows. Call Next, Recall, Complete, Hold, Call Held, and per-window timers.

Public display. Serving and waiting numbers in fair order; no names; no held tickets.

Administrator dashboard. Users, students, kiosk PIN, walk-in issue, history, and done-only reports.

#### 3.4.3 Unified Modeling Language (UML)

##### 3.4.3.1 Use Case Diagram

Student/Client: select service, select Regular or Priority, type or tap Student ID, optionally turn on **Notify me by SMS** (off by default; stored student mobile only), view ETA when available, confirm, receive printed number.

Walk-in path: enter kiosk PIN, issue walk-in ticket, receive printed number.

Staff: log in, view waiting list, Call Next, Recall, Complete, Hold, Call Held, view name or walk-in reason while serving, receive a paper-out alert on the staff dashboard.

Administrator: log in, manage users, manage students (RFID/NFC card UID and mobile), set kiosk PIN, issue walk-in ticket, view reports.

Public display: show Now Serving and waiting numbers only.

Database: store students, settings, queues (`student_id` or `issued_by` / `issue_reason`), counters, and queue calls.

##### 3.4.3.2 Activity Diagram

The student path is service → priority → type or tap Student ID confirm → optionally turn on SMS (stored mobile) → generate → print → optional SMS → wait on display → staff call → serve or hold → complete. The walk-in path substitutes PIN unlock, reason, and optional overlay mobile for Student ID. Staff may recall, hold, or resume before complete. The display updates after each status change.

##### 3.4.3.3 Sequence Diagram

The client and kiosk send service, priority, and Student ID (typed or tapped) or walk-in PIN data to the application. The application validates, writes the queue and counter, and sends print data. Staff actions update queue_calls and queues and may queue GSM SMS. The display polls serving and waiting data. Names never go to print, SMS, or the public display.

#### 3.4.4 Data Flow Diagram (DFD)

##### 3.4.4.1 Level 0 – Context Diagram

External entities are Client, Staff, and Administrator. The client sends service, priority, Student ID or walk-in PIN and reason, and receives a ticket and display information. Staff send Call Next, Recall, Complete, Hold, and Call Held. The administrator sends user, student, PIN, and report requests and receives records and reports.

##### 3.4.4.2 DFD Level 1

Level 1 expands confirmation and validation, walk-in unlock, number generation, printing, display update, staff queue control, and administrative recording. Data stores are students, settings, services, windows, users, daily_queue_counters, queues, and queue_calls.

#### 3.4.5 Entity Relationship Diagram (ERD)

The principal entities are services, windows, users, students, settings, daily_queue_counters, queues, and queue_calls.

Services relate to windows, queues, and daily_queue_counters. Students supply the allowlist used when `queues.student_id` is present. Walk-in tickets leave `student_id` null and store `issued_by` and `issue_reason`. Users have role `admin`, `staff`, or `guard`. Staff users have a unique `window_id`. The guard user cannot log in. Settings store `walkin_pin`. Queue_calls link a ticket to a window with `called_time` and `finished_time`. Any ERD drawing placed in the manuscript is a placeholder and must be replaced so it shows these entities.

#### 3.4.6 Database Design

The database is MySQL/MariaDB. Domain tables are services, windows, users, students, settings, daily_queue_counters, queues, and queue_calls. Laravel `migrations` may exist in the environment but is not a queue-domain table.

##### 3.4.6.1 Database Schema

`services` stores service name and ticket prefix. `windows` stores window name, group, assigned service, and status. `users` stores name, login, password, role, and optional unique window. `students` stores authorized Student ID and name. `settings` stores `walkin_pin`. `daily_queue_counters` stores last number per service and date. `queues` stores number, service, optional student, optional issuer and reason, priority, status, date, and timestamps. `queue_calls` stores queue, window, called time, and finished time. Any schema drawing placed in the manuscript is a placeholder and must be replaced so it matches this structure.

##### 3.4.6.2 Data Dictionary

**Table 3.4 Data Dictionary – Services**

| Field Name | Data Type | Key | Description |
| --- | --- | --- | --- |
| id | BIGINT | PK | Service identifier |
| service_name | VARCHAR(50) | — | Service name |
| prefix | VARCHAR(5) | — | Ticket prefix (C, R, D, P) |
| description | TEXT | — | Service description |
| created_at | TIMESTAMP | — | Record created |
| updated_at | TIMESTAMP | — | Record updated |

**Table 3.5 Data Dictionary – Windows**

| Field Name | Data Type | Key | Description |
| --- | --- | --- | --- |
| id | BIGINT | PK | Window identifier |
| window_name | VARCHAR(50) | — | Window name (Cashier 1, Registrar) |
| group_name | VARCHAR(50) | — | Display group |
| service_id | BIGINT | FK | Assigned service |
| status | ENUM | — | active or inactive |
| created_at | TIMESTAMP | — | Record created |
| updated_at | TIMESTAMP | — | Record updated |

**Table 3.6 Data Dictionary – Users**

| Field Name | Data Type | Key | Description |
| --- | --- | --- | --- |
| id | BIGINT | PK | User identifier |
| name | VARCHAR(100) | — | User name |
| email | VARCHAR(100) | — | Login email |
| password | VARCHAR(255) | — | Password (bcrypt or accepted legacy dump value) |
| role | ENUM | — | admin, staff, or guard |
| window_id | BIGINT | FK, unique | Staff window; null for admin and guard |
| created_at | TIMESTAMP | — | Record created |
| updated_at | TIMESTAMP | — | Record updated |

Guard exists as the kiosk issuer row and cannot log in.

**Table 3.7 Data Dictionary – Students**

| Field Name | Data Type | Key | Description |
| --- | --- | --- | --- |
| id | BIGINT | PK | Record identifier |
| student_id | VARCHAR(50) | Unique | Authorized Student ID |
| name | VARCHAR(100) | — | Student name for confirm and staff serving |
| card_uid | VARCHAR(32) | Unique, nullable | Enrolled RFID/NFC serial for tap confirm |
| mobile | VARCHAR(20) | — | Stored number for GSM SMS; not typed at student Confirm |
| created_at | TIMESTAMP | — | Record created |
| updated_at | TIMESTAMP | — | Record updated |

**Table 3.8 Data Dictionary – Settings**

| Field Name | Data Type | Key | Description |
| --- | --- | --- | --- |
| id | BIGINT | PK | Setting identifier |
| setting_key | VARCHAR(64) | Unique | Setting name (`walkin_pin`) |
| setting_value | VARCHAR(255) | — | Stored value |
| updated_at | TIMESTAMP | — | Last update |

**Table 3.9 Data Dictionary – Daily Queue Counters**

| Field Name | Data Type | Key | Description |
| --- | --- | --- | --- |
| id | BIGINT | PK | Counter identifier |
| service_id | BIGINT | FK | Service |
| queue_date | DATE | — | Operating date |
| last_number | INT | — | Last issued number |
| created_at | TIMESTAMP | — | Record created |
| updated_at | TIMESTAMP | — | Record updated |

**Table 3.10 Data Dictionary – Queues**

| Field Name | Data Type | Key | Description |
| --- | --- | --- | --- |
| id | BIGINT | PK | Ticket identifier |
| queue_number | VARCHAR(20) | — | Printed number |
| service_id | BIGINT | FK | Selected service |
| student_id | VARCHAR(50) | FK, nullable | Student ticket only |
| issued_by | BIGINT | FK, nullable | Walk-in issuer user id |
| issue_reason | VARCHAR(32) | — | Walk-in reason |
| priority | TINYINT | — | 0 Regular, 1 Priority |
| status | ENUM | — | waiting, serving, done, cancelled, held |
| queue_date | DATE | — | Issue date |
| created_at | TIMESTAMP | — | Used for wait averages |
| notify_mobile | VARCHAR(20) | — | Ticket SMS number: copied from `students.mobile` when SMS is on, or entered on the walk-in overlay |
| sms_near_sent | TINYINT | — | 1 after the five-tickets-away SMS |
| updated_at | TIMESTAMP | — | Last update |

Name is never stored on the ticket row. Walk-in tickets leave `student_id` null.

**Table 3.11 Data Dictionary – Queue Calls**

| Field Name | Data Type | Key | Description |
| --- | --- | --- | --- |
| id | BIGINT | PK | Call identifier |
| queue_id | BIGINT | FK | Ticket |
| window_id | BIGINT | FK | Serving window |
| called_time | DATETIME | — | Time called; open call when finished_time is null |
| finished_time | DATETIME | — | Time completed or hold session closed |
| created_at | TIMESTAMP | — | Record created |
| updated_at | TIMESTAMP | — | Record updated |

Reports use the latest completed call of a `done` ticket. Hold sessions are excluded from served counts.

**Table 3.12 Data Dictionary Summary**

| Table name | Main purpose |
| --- | --- |
| services | Stores available services and queue prefixes |
| windows | Stores service counters and their assigned services |
| users | Stores authorized administrator and staff accounts |
| students | Stores authorized Student IDs, names, optional card serials (UID), and optional mobile numbers |
| settings | Stores the kiosk walk-in PIN (`walkin_pin`); the PIN is not published here |
| daily_queue_counters | Maintains the last queue number per service and date |
| queues | Stores generated queue tickets and their statuses |
| queue_calls | Records calls, windows, called time, and finished time |

#### 3.4.7 System Flowchart

The client uses the kiosk. A student completes three steps and Student ID validation by keypad or RFID/NFC tap. After lookup, **Notify me by SMS** is off by default and uses the stored student mobile; students do not type a number. A walk-in user unlocks with the PIN, enters a reason, and may enter a mobile number on the overlay. The system increments the daily counter, stores the ticket, prints the number-only 58mm ticket, and updates the display. Optional GSM SMS is sent when an opted-in ticket is five places away and when it is called. Staff call by 2 Priority → 1 Regular, or resume a held ticket. Status moves among waiting, serving, held, and done. The administrator reviews users, students, card UID and mobile enrollment, PIN, and done-only reports. The system stays on the LAN. Any flowchart drawing placed in the manuscript is a placeholder and must be replaced so it shows the typed-ID path, the RFID/NFC tap path, the walk-in PIN path, GSM SMS notices, plus Hold and Call Held.

#### 3.4.8 User Interface Design

The implemented interfaces are described from the current system. Screenshots or wireframes placed beside this section are placeholders and must be replaced with captures of the live kiosk, staff panel, administrator dashboard, and public display. The text below is the description of record.

Kiosk. The public kiosk is a three-step flow on a compact 1024×600 layout: Cashier, Registrar, and DMO; Regular or Priority; then confirmation with a summary column, Student ID keypad, and RFID/NFC reader. Confirm stays disabled until lookup succeeds. The matched name may appear for checking only. Waiting counts and estimated wait may appear when data are sufficient. **Notify me by SMS** is off by default and uses the stored student mobile; students do not type a number. If no mobile is on file, the control is disabled and shows **No SMS number on file**. The full number is not shown. Promissory Notes is hidden. A small bottom-right control opens the walk-in PIN pad on every use. After a correct PIN, the overlay issues a walk-in ticket with service, priority, reason, and an optional mobile number. After print, a countdown returns to the first step. The student name is never printed.

Staff panel. Authorized staff see the current number, student name or Walk-in (reason), an independent window timer, a fair waiting list of up to 10, and a held list. Controls are Call Next, Recall, Complete, Hold, and Call Held. Shortcuts are Alt+N, Alt+R, and Alt+C. An optional System Float window can stay on top on the staff PC. Staff history shows that user’s own done tickets. When the thermal printer has no paper, a paper-out alert appears on the staff dashboard so the roll can be replaced before the next client is blocked.

Administrator dashboard. The PECIT panel shows live totals and waiting information, users, students (add, delete, CSV import, RFID/NFC card UID and mobile), kiosk PIN, walk-in issue, history, all tickets including held, and done-only wait and service reports. There is no public Services CRUD screen.

Public display. Windows are grouped by `group_name`: Window 1 Cashier 1–3, Window 2 Promissory Notes, Window 3 DMO, Window 4 Registrar. The screen shows Now Serving and up to 10 waiting numbers in the same fair order as Call Next. Names and held tickets are omitted. Voice announcement may be enabled.

### 3.5 Operational Environment

#### 3.5.1 Hardware Requirements

**Table 3.13 Hardware Requirements**

| Hardware Component | Configuration |
| --- | --- |
| Mini PC (kiosk and server) | Intel Core i5 8th Generation, 8 GB RAM; hosts the kiosk UI and Laravel/XAMPP |
| Touchscreen monitor | Public three-step kiosk, attached to the mini PC |
| 58mm USB thermal printer | XP-58 (XP-Q90EC); paper-out alert on the staff dashboard |
| RFID/NFC reader | ACR122U-A9 USB, ISO 14443A / MIFARE, at kiosk confirm |
| GSM/LTE USB module | SIM7600G-H on the mini PC; SIM and load required |
| HDMI monitor | Public queue display |
| AVR HDMI box | Display connection |
| Staff workstation | One per window |
| Administrator computer | Users, students, PIN, reports |
| Keyboard and mouse | Standard USB |
| LAN router or switch | Local communication |
| Cables and adapters | USB, HDMI, AVR, Ethernet, power |

#### 3.5.2 Software Requirements

**Table 3.14 Software Requirements**

| Software | Purpose |
| --- | --- |
| Windows 10/11 | Host and workstation OS |
| XAMPP | Local Apache/PHP/MySQL |
| PHP 8.2+ | Server runtime |
| Laravel 12 | Application framework |
| Blade, Vite, Tailwind CSS | Interfaces and assets |
| MySQL/MariaDB | Local database |
| Visual Studio Code | Development editor |
| Composer, Node.js/npm | Dependencies |
| Chrome or Edge | Kiosk, staff, display, admin |
| Git | Version control |

#### 3.5.3 Network Requirements

The kiosk, application host, staff PCs, display, and administrator computer communicate on the campus LAN. The application host is the same mini PC that drives the kiosk. Wired Ethernet is preferred. Continuous internet access is not required for ticket issuance, calling, display, or reports [11]. GSM SMS uses the SIM7600G-H module and a loaded SIM.

#### 3.5.4 User Environment

Students and parents use the kiosk and public display. Walk-in clients use the PIN path. Staff use the window panel and optional System Float. The administrator uses the panel for users, students, PIN, walk-in issue, and reports. The Guard role has no login screen.

### 3.6 System-to-System Interface

The kiosk posts confirmed student or walk-in requests to the Laravel application. The application writes queues and counters, then the kiosk prints. Staff posts change queue_calls and queue status. The display and staff panel read serving, waiting, and held data. The administrator reads and writes users, students, and settings.

### 3.7 Communication Interface

#### 3.7.1 Internal Communication

**Table 3.15 Internal Communication Interface**

| Source | Destination | Data | Purpose |
| --- | --- | --- | --- |
| Kiosk | Application | Service, priority, Student ID or walk-in PIN/reason | Validate and create ticket |
| Application | Database | Queue, student or issuer, priority, status, counters, settings | Store and retrieve |
| Application | Thermal printer | Number and optional ETA | Print number-only ticket |
| Mini PC | Staff panel | Printer paper status | Paper-out alert on the staff dashboard |
| Database | Staff panel | Waiting, serving, held, name or walk-in reason | Window operations |
| Database | Admin dashboard | Users, students, PIN, history, reports | Administration |
| Database | Public display | Serving and waiting numbers | Public monitoring |

#### 3.7.2 External Communication

**Table 3.16 External Communication Interface**

| External Entity | Interface | Information sent | System response |
| --- | --- | --- | --- |
| Students and parents | Kiosk | Service, priority, Student ID, confirm | Validated ticket and print |
| Walk-in clients | Kiosk PIN overlay | PIN, service, priority, reason | Walk-in ticket and print |
| Students and parents | Public display | — | Serving and waiting numbers |
| Administrative staff | Staff panel | Call Next, Recall, Complete, Hold, Call Held | Updated window and display; paper-out alert when the printer has no paper |
| System administrator | Admin panel | Users, students, PIN, reports | Authorized administration |

### 3.8 Development Methodology

The system was developed using the Waterfall model because the offices, kiosk hardware, printer, LAN, and queue rules were known before coding [19]. The five phases were Requirements Analysis, System Design, Implementation/Development, Testing, and Deployment.

*Figure 3.11 Waterfall Development Model Used in the Study*

#### 3.8.1 Software Development Model

Requirements analysis used the questionnaire, interviews, observation, and document review. Design produced the architecture, use cases, DFD, ERD, schema, flowchart, and interfaces. Implementation used Laravel 12, PHP, Blade, Vite/Tailwind, and MySQL/MariaDB, including FairQueueScheduler, WaitTimeEstimator, StudentQueueGuard, and KioskWalkInGate. Testing covered the functions listed in Section 3.11. Deployment configured the mini PC as both kiosk and server, the XP-Q90EC printer, ACR122U-A9 reader, SIM7600G-H module, staff PCs, display, and LAN.

#### 3.8.2 Development Approach

Each phase was completed before the next. The kiosk chrome was kept as a locked three-step flow. Staff and administrator screens used the PECIT panel theme. Queue rules were implemented in server-side services rather than in the browser alone.

#### 3.8.3 Development Tools

**Table 3.17 Development Tools**

| Tool | Purpose |
| --- | --- |
| Laravel 12 | Backend and routing |
| PHP 8.2+ | Server language |
| MySQL/MariaDB | Data store |
| XAMPP | Local server |
| Visual Studio Code | Editing |
| Blade, Vite, Tailwind CSS | Interfaces |
| JavaScript | Kiosk keypad, display poll, staff poll |
| Chrome / Edge | Runtime and kiosk printing |
| Git | Source history |

#### 3.8.4 Version Control

Git recorded source changes so modules could be revised without losing earlier working versions.

#### 3.8.5 Deployment Strategy

The application is installed on the local server or kiosk host. Staff and display PCs open the LAN URL. The thermal printer and RFID/NFC reader are attached to the kiosk PC. The GSM/LTE module is attached to the application host. Chrome kiosk flags support silent printing. Staff PCs may use the System Float helper. The database and session files remain local.

### 3.9 System Workflow and Operations

#### 3.9.1 System Workflow

The student selects service and priority, types or taps a Student ID, may turn on **Notify me by SMS** (off by default; stored mobile), and confirms. The application validates the ID and the one-open-ticket rule, then prints. A walk-in user unlocks with the PIN, enters a reason, may enter a mobile number on the overlay, and receives a number-only ticket. Staff call from the shared fair list or resume a held ticket. Optional GSM SMS is sent at five tickets away and when called. Completed tickets supply report averages.

#### 3.9.2 Operational Workflow

1. Client opens the kiosk.
2. Client selects Cashier, Registrar, or DMO.
3. Client selects Regular or Priority.
4. Student path: type or tap Student ID, wait for lookup, optionally turn on **Notify me by SMS** if a number is on file, confirm.
5. Walk-in path: corner control, PIN, reason, confirm.
6. System generates and stores the number.
7. Printer issues the 58mm ticket.
8. Display and staff lists update. Optional GSM SMS is queued when applicable.
9. Staff Call Next, Recall, Hold, Call Held, or Complete.
10. Completed tickets appear in history and reports.

#### 3.9.3 Module Interaction

**Table 3.18 Module Interaction**

| Module | Primary function | Interacts with |
| --- | --- | --- |
| Kiosk | Three-step student flow and PIN walk-in | Application, database, printer |
| Application | Validation, numbering, scheduling, ETA, PIN check | All modules |
| Database | Students, settings, queues, calls | Application |
| Thermal printer | Number-only ticket | Kiosk / application |
| Staff panel | Call Next, Recall, Complete, Hold, Call Held, paper-out alert | Application, database, printer status |
| Public display | Fair serving and waiting numbers | Application, database |
| Administrator | Users, students, PIN, walk-in issue, reports | Application, database |

#### 3.9.4 Input–Process–Output Workflow

**Table 3.19 Input–Process–Output Workflow**

| Input | Process | Output |
| --- | --- | --- |
| Service and priority | Validate selection | Ready confirmation |
| Student ID | Lookup and one-open-ticket check | Validated student request |
| Walk-in PIN and reason | Server PIN check | Authorized walk-in request |
| Valid request | Increment counter and store queue | Queue record |
| Queue record | Print 58mm ticket | Physical number reference |
| Queue and call updates | Refresh display and staff lists | Serving, waiting, held views |
| Complete | Mark done and close call | History and report sample |

### 3.10 Project Management Plan

The plan followed Waterfall from requirements through deployment and documentation. A Gantt chart recorded the sequence of chapter writing, design, development, testing, evaluation, and submission.

#### 3.10.1 Gantt Chart

**Table 3.20 Gantt Chart for January to May**

| Activity | January | February | March | April | May |
| --- | --- | --- | --- | --- | --- |
| Topic identification and title | Weeks 1–4 | | | | |
| Chapter 1 | | Weeks 1–4 | | | |
| Chapter 2 | | | Weeks 1–4 | | |
| Chapter 3 | | | | Weeks 1–4 | Weeks 1–2 |

**Table 3.21 Gantt Chart for June to October**

| Activity | June | July | August | September |
| --- | --- | --- | --- | --- |
| System analysis and design | Weeks 1–4 | | | |
| System development | | Weeks 1–4 | | |
| Testing and debugging | | | Weeks 1–3 | |
| Deployment and evaluation | | | Week 4 | Weeks 1–2 |
| Chapters 4 and 5 and final editing | | | | Weeks 2–4 |

#### 3.10.2 Budget and Resource Allocation

Hardware used for development and testing was already available and is not treated as a purchased project cost.

**Table 3.22 Hardware Resources**

| Item | Quantity | Remarks |
| --- | --- | --- |
| Mini PC (kiosk and server) | 1 | Intel Core i5 8th Generation, 8 GB RAM |
| Touchscreen monitor | 1 | Public kiosk display |
| 58mm USB thermal printer | 1 | XP-Q90EC ticket printing |
| USB RFID/NFC reader | 1 | ACR122U-A9 at kiosk confirm |
| GSM/LTE USB module | 1 | SIM7600G-H on the mini PC |
| HDMI monitor | 1 | Public display |
| Staff workstation | 1 or more | Window operations |
| Administrator computer | 1 | Administration |
| LAN router or switch | 1 | Local network |
| Cables and adapters | As needed | Device connections |

#### 3.10.3 Risk Management Plan

**Table 3.23 Risk Management Plan**

| Potential risk | Possible impact | Mitigation |
| --- | --- | --- |
| Printer out of paper | No ticket print | Staff dashboard paper-out alert; spare roll; reprint from stored ticket |
| Power interruption | Temporary stop | Local file/database recovery after restart |
| LAN failure | Display and staff out of sync | Inspect switch and cables before opening |
| Software defect | Wrong ticket or call | Unit, integration, and UAT before deployment |
| Student list error | Eligible ID rejected | Admin student maintenance and clear kiosk errors |
| Wrong walk-in PIN use | Unauthorized walk-in | Server-side PIN, ask PIN on every corner click |
| Schedule delay | Late documentation | Follow the Gantt and keep testing time |

### 3.11 Verification, Validation, and Testing Plans

Testing followed implementation and supported Specific Objective 5: evaluate functionality, usability, efficiency, reliability and accuracy, and user satisfaction through system testing, expert evaluation, and UAT using ISO/IEC 25010-aligned characteristics [10].

#### 3.11.1 Test Planning

**Table 3.24 Test Planning**

| Testing activity | Objective | Expected outcome |
| --- | --- | --- |
| Test-case preparation | Cover kiosk, walk-in PIN, staff, display, admin | Complete cases |
| Environment setup | Kiosk, printer, LAN, staff, display | Ready test bed |
| Functional testing | Verify rules and modules | Requirements covered |
| Integration testing | Verify component exchange | Consistent ticket and display state |
| UAT and expert evaluation | Measure acceptance and quality | Documented ratings |

#### 3.11.2 Unit Testing

Unit testing checked service and priority selection, Student ID lookup, one-open-ticket rejection, walk-in PIN accept/reject, counter increment, print payload, paper-out alert, fair claim, Hold, Call Held, complete, and report filters.

#### 3.11.3 Integration Testing

Integration testing verified that a confirmed student or walk-in ticket appears in the staff list and display, that Call Next on two cashier windows cannot claim the same ticket, that Hold removes the number from Now Serving, and that administrator reports exclude hold sessions.

#### 3.11.4 System Testing

System testing covered the full path on the LAN at the selected PECIT offices: kiosk, printer, staff, display, and administrator. After the system was implemented, User Acceptance Testing used the Cashier, Registrar, DMO, and Student System Feedback Forms. One IT expert who tested the developed system completed the Technical Expert System Evaluation Form. Expert ratings are not combined with the UAT means. The Chapter 3 student-and-parent questionnaire is the needs-assessment survey and is not reused as the Chapter 4 evaluation.

### 3.12 Ethical Considerations

Permission was obtained from PECIT before data gathering and testing. Participation was voluntary. Only information needed for queue validation and management was collected, consistent with the Data Privacy Act of 2012 and its implementing rules [18], [14]. Student ID is used for confirmation and staff serving. Names are not printed or shown on the public display. Walk-in reasons are visible to staff only. The walk-in PIN is stored in settings and checked on the server. Database access is limited to authorized accounts. Findings are reported without fabrication.

These procedures protect participants while allowing the system to replace the manual first-come-first-served line with an organized, monitored, and recorded queue process.
