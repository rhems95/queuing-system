# Standalone Kiosk-Based Queuing Management System with Thermal Printer for Student Service Offices at Philippine Electronics and Communication Institute of Technology (PECIT)

A Capstone Project Presented to the Faculty of the College of Computer Studies  
Philippine Electronics and Communication Institute of Technology  
Butuan City, Philippines

In Partial Fulfillment of the Requirements for the Degree  
**Bachelor of Science in Information Systems**

**Authors**

- Rhem A. Sumodlayon
- Mike D. Bedrona
- Niño Angelo M. Malicay
- Omar O. Bayabao
- Ron Matthew F. Arquisola

August 2026

**Manuscript format (for Microsoft Word).** Body text is **Arial, 12 pt**, except the cover page. Preliminary pages (Dedication, Acknowledgement, Abstract, Table of Contents) use **lowercase Roman numerals centered at the bottom** of the page. Chapter 1 onward uses Arabic page numbers. Citations follow **IEEE**. The reference list is **APA 7th edition**, **alphabetical by author**, then numbered.

---



# DEDICATION

*(Preliminary page. In Word: lowercase Roman numeral, centered at the bottom.)*

This capstone project is dedicated to our families. Their patience, prayers, and support carried the researchers through the long work of analysis, programming, testing, writing, and revision.

It is also dedicated to the students, parents, and walk-in clients of the Philippine Electronics and Communication Institute of Technology who wait for service at the Cashier, Registrar, and Data Management Office, and to the staff who serve them during enrollment, payment, and records periods.

The researchers further dedicate this work to the College of Computer Studies and the College of Information System, under the academic leadership of Daryll A. Cabagay, MSIT, Dean, and to the faculty who trained us in information systems. May this standalone kiosk-based queuing system with thermal printer be a modest return to the institution that formed us.

# ACKNOWLEDGEMENT

*(Preliminary page. In Word: lowercase Roman numeral, centered at the bottom.)*

The researchers express their sincere gratitude to Daryll A. Cabagay, MSIT, Dean of the College of Information System, for the academic leadership and institutional support that made this capstone project possible.

The researchers thank Josephine U. Acha, Ph.D., Research and Extension Director, for promoting a culture of inquiry at PECIT and for the research standards that guided the preparation of this manuscript.

The researchers also thank Wilson Rey Sevilla and Zoren Estrada Bajao, former and current program heads, and the rest of the College of Computer Studies and Information Systems faculty for instruction, consultation, and the reminder that a campus system must fit PECIT rather than a generic template. We likewise thank our capstone adviser and the members of the oral defense panel for their questions and comments on the problem, method, and implementation.

We are grateful to the PECIT administration and to the staff of the Cashier, Registrar, and Data Management Office. They described the manual first-come-first-served line and allowed the researchers to observe how windows, priority clients, and walk-ins are handled in daily operations.

Finally, the researchers thank their families and one another—Mike D. Bedrona, Niño Angelo M. Malicay, Omar O. Bayabao, Rhem A. Sumodlayon, and Ron Matthew F. Arquisola—for the shared work that produced this system and this manuscript.

# ABSTRACT

*(Preliminary page. In Word: lowercase Roman numeral, centered at the bottom.)*

Student service offices at the Philippine Electronics and Communication Institute of Technology (PECIT) currently process Cashier, Registrar, and Data Management Office (DMO) transactions through manual first-come-first-served lining. That physical line can produce unclear order, line cutting, weak priority handling, crowding, and no shared view of who is waiting. This capstone developed and implemented a standalone Local Area Network (LAN)–based kiosk queuing system with a 58mm thermal printer as a localized replacement for that process. One mini PC (Intel Core i5 8th Generation, 8 GB RAM) serves as both the public kiosk and the application server. The kiosk uses an ACR122U-A9 RFID/NFC reader and a SIM7600G-H GSM/LTE module. When the printer is out of paper, the staff window and System Float show a paper-out alert, and checked staff or assistant numbers may receive one SMS.

The student kiosk remains three steps: service selection, Regular or Priority, and confirmation. Confirmation requires a validated Student ID typed on the keypad or tapped on an enrolled RFID/NFC card, or an authorized walk-in ticket after the kiosk PIN is accepted. Clients who do not bring the card may still type the Student ID. **Notify me by SMS** is off by default and uses the mobile number stored on the student record; students do not type a number at Confirm. If no mobile is on file, the control stays off and disabled and shows that no SMS number is on file; print still proceeds. Walk-in clients may leave the mobile blank or enter a Philippine number (09 and 9 more digits) on the PIN overlay; any other number keeps Issue and Print disabled. Each walk-in receives a screen token such as T1006ABC (weekday initial, month, day, and three letters), shown on the cooldown screen and not printed. The printed ticket and public display stay number-only. GSM SMS may send the ticket number after print, a come-back text when the ticket first has 10 or fewer ahead in fair call order, and the window name when it is called. Staff windows that share a service, including Cashier 1 to Cashier 3, use one waiting queue and a service-wide 2 Priority → 1 Regular call order, with Hold and Call Held. Administrators manage users, the student allowlist, RFID/NFC card UID and mobile enrollment, the walk-in PIN, and reports based on completed transactions only.

Development followed the Waterfall model. Evaluation used functional tests, one technical expert review, and User Acceptance Testing aligned with ISO/IEC 25010 product-quality characteristics. User feedback was **Strongly Agree** (Cashier **5.00**, Registrar **5.00**, DMO **4.92**, Student form **4.91**; combined **4.96**). The IT expert scored **5.00** overall. The study does not claim a measured reduction in waiting time.

**Keywords:** **queue management**, **kiosk**, **thermal printer**, **LAN**, **Student ID**, **RFID/NFC**, **GSM SMS**, **priority scheduling**, **ISO/IEC 25010**, **PECIT**

# TABLE OF CONTENTS

*(Preliminary page. In Word: lowercase Roman numeral, centered at the bottom.)*

- [Dedication](#dedication)
- [Acknowledgement](#acknowledgement)
- [Abstract](#abstract)
- [Chapter 1 Introduction](#chapter-1)
- [Chapter 2 Review of Related Literature and Studies](#chapter-2)
- [Chapter 3 Research Methodology](#chapter-3)
- [Chapter 4 System Implementation, Results, and Discussion](#chapter-4)
- [Chapter 5 Summary, Conclusions, and Recommendations](#chapter-5)
- [References](#references)
- [Curriculum Vitae](#curriculum-vitae)

---



# CHAPTER 1



## INTRODUCTION



### 1.1 Background of the Study

Efficient service delivery is an important aspect of institutional operations, particularly in offices that handle a high volume of transactions within limited periods. In educational institutions, student service offices commonly manage activities such as payments, enrollment-related concerns, academic records, document requests, and verification. At PECIT, these transactions are currently handled through manual first-come-first-served lining. When clients depend only on physical arrival order, they may experience long lines, uncertainty about service order, overcrowded waiting areas, line cutting, and difficulty determining when their turn will be served [12]. Administrative personnel may likewise experience challenges in coordinating clients, serving priority cases, and monitoring the movement of transactions across several windows.

The development of digital technologies has encouraged organizations and educational institutions to adopt computerized systems that improve the organization and delivery of services. In the Philippine higher education sector, digital transformation has become increasingly relevant to institutional support services. The Commission on Higher Education Regional Office XI [5] has implemented and promoted digital transformation initiatives involving higher education institutions, including automated frontline services and a queueing system with online appointment and automated printing capabilities. These developments demonstrate the growing relevance of technology-assisted processes in improving administrative service delivery.

Self-service kiosk technology provides another approach for organizing the initial stage of service transactions. Kiosks allow clients to interact directly with a computerized interface to select a service and initiate a transaction. In campus service settings, digitized queue entry has been used to standardize how clients join a line, obtain a queue reference, and reduce dependence on informal physical lining [12]. In a student service environment, a kiosk can provide a structured process for selecting a service, choosing a queue type, confirming the request, and receiving a printed queue ticket.

Automated queueing has also been applied specifically in Philippine higher education institutions. Espinosa et al. [7] developed an automated enrollment queueing system for a university to address problems related to student waiting lines, overcrowding, and employee workload. Their study reported that an automated queueing approach could provide a more organized and controlled enrollment process and improve the experience of students and guardians. Pantonial et al. [16] examined queue management and cashier efficiency in a private higher education institution and highlighted the relationship between queue conditions, student payment behavior, and cashier productivity. Ramoga [17] also examined a queue management system for the Registrar and Cashier offices of Jose Rizal Memorial State University–Dipolog Campus, addressing manual queuing problems such as long waiting times, crowded service areas, and communication gaps. These studies establish that queue management remains a relevant concern in Philippine higher education service environments.

Despite these developments, existing queue management implementations differ in their architecture, scope, and combination of functions. Some systems focus on enrollment or specific office processes, while others use online, appointment-based, ticketless, or hybrid approaches [7], [17]. This indicates a continuing need for queue management solutions that are designed around the particular operational conditions of an institution. UNESCO [22] emphasizes that educational technology should be selected according to relevance, institutional context, and sustainability rather than adopted merely because it is available. For PECIT, such a solution needs to coordinate multiple student service offices and service windows while providing a clear process for queue generation, priority handling, ticket issuance, queue monitoring, staff control, and administrative reporting.

A standalone, Local Area Network (LAN)-based architecture is appropriate for an institutional environment where continuous internet connectivity is not required for essential queue operations [22], [9]. In the developed system, the kiosk, staff workstations, public display, administrator computer, and local database communicate within the institution's local network. The system is built using Laravel 12, Blade, Vite/Tailwind, and MySQL/MariaDB and is intended for local deployment through the institution's available computing and network resources.

The proposed system is specifically designed for the student service offices of the Philippine Electronics and Communication Institute of Technology (PECIT). Through the touchscreen kiosk, clients select a service, choose either Regular or Priority, and proceed to a confirmation stage. During confirmation the Student ID is typed on the keypad or read from an enrolled RFID/NFC student ID card at the kiosk reader, then validated before a queue ticket is generated. Clients who do not bring the physical ID may still type the Student ID. Clients who cannot use a Student ID may request a walk-in ticket at the same kiosk after entering the designated kiosk PIN. When sufficient historical and current queue information is available, the confirmation stage may also provide an estimated waiting time. A **Notify me by SMS** control is **off by default**. Students do not type a mobile number at the kiosk; notices use the mobile number stored with the Student ID and RFID/NFC card serial in the student list. Walk-in clients have no student record and may leave the mobile blank or enter a Philippine number (`09` and 9 more digits) only on the PIN overlay. Each walk-in receives a screen token, such as T1006ABC, shown on the cooldown screen and not printed. The student kiosk flow remains a three-step process of service selection, priority selection, and confirmation before ticket generation.

The staff component supports coordinated queue management across service windows. Multiple windows assigned to the same service can share one waiting queue. In the Cashier service, for example, Cashier 1, Cashier 2, and Cashier 3 share a common Cashier waiting queue while maintaining independent active transactions and service timers. The system applies a service-wide 2 Priority → 1 Regular scheduling rule and provides authorized staff with functions to call the next queue, recall a queue, place a transaction on hold, resume a held transaction, and complete a transaction. While serving, staff may view the student name associated with a validated Student ID, or the walk-in reason and screen token for an authorized walk-in ticket.

The system also provides a public queue display showing current serving numbers and waiting queues without exposing student names publicly. Queue and service activities are recorded in the local database, enabling administrators to manage authorized users and student records, set the kiosk walk-in PIN, monitor queue activities, review transaction history, and generate reports containing waiting-time and service-time information from completed transactions only.

Although automated queueing systems have already been implemented in different educational and service environments, there remains a need for a localized and integrated solution specifically tailored to PECIT's student service operations [9]. The distinct contribution of the present study is therefore not simply the use of a standalone queueing system, but the integration of a standalone, LAN-based kiosk, thermal ticket printing, Student ID validation by keypad or RFID/NFC tap, authorized kiosk-PIN walk-in issuance, local GSM SMS notices, priority queue scheduling, multi-counter queue management, real-time public display, staff queue controls, transaction recording, and administrative reporting within a single system designed around the operational requirements of PECIT. Through this approach, the study seeks to provide a practical, organized, transparent, and institution-specific queue management solution for PECIT's student service offices.

### 1.2 Research Gap

Kiosks, public displays, Student ID entry, SMS notices, and online appointments have already been used in queue studies. This study does **not** treat those tools as new inventions.

What is still missing is a **standalone LAN design for PECIT**. Some Philippine systems depend on the web or online appointments [7], [17]. Others drop the paper ticket and rely on SMS. PECIT’s Cashier, Registrar, and DMO still use a **manual first-come-first-served line with no ticket**, and daily queue work must continue when the internet is down [22].

Prior work also tends to cover **one office or one function**—enrollment lines or cashier behavior—rather than **Cashier, Registrar, and DMO in one platform**. Few systems combine kiosk confirmation (**type or RFID/NFC tap**), PIN walk-in, **58mm number-only print**, local GSM SMS, **2 Priority → 1 Regular** across shared windows, Hold and Call Held, and done-only reports on a campus LAN.

The gap this study fills is **integration and localization**: a PECIT-specific kiosk queue that replaces the physical line, not a claim that any single device is original.

### 1.3 Statement of the Problem



#### 1.3.1 General Problem

The Philippine Electronics and Communication Institute of Technology (PECIT) currently handles Cashier, Registrar, and Data Management Office (DMO) transactions through manual first-come-first-served lining. Clients wait in a physical line and do not receive a queue ticket. Service order depends only on who arrived first. This process can produce unclear service order, line cutting, difficulty serving priority clients, and weak coordination when several windows work at once. These conditions indicate the need for a more organized and technology-supported queue management process.

Thus, the **general problem** of this study is:

**How can a standalone kiosk-based queuing management system be developed and implemented to replace PECIT’s current first-come-first-served lining, which uses no queue ticket, with an organized, monitored, and coordinated queue process?**

#### 1.3.2 Specific Problems

Specifically, this study seeks to answer the following questions:

1. How can student transactions at the Cashier, Registrar, and DMO be organized when clients currently wait in a **manual first-come-first-served line** that uses **no queue ticket** and is subject to line cutting?
2. How can a client’s request be checked and confirmed—through Student ID validation by keypad or RFID/NFC tap, or authorized walk-in issuance—before a queue number is given, instead of relying only on physical arrival?
3. How can clients have a clear queue reference when the current process issues no ticket, and how can priority clients be served fairly while several windows share one service queue, instead of depending only on a first-come-first-served physical line?
4. How can clients and service windows track who is being served and who is waiting when the current process has no real-time queue display, and how can a student who opts in to SMS—or a walk-in who gives a Philippine mobile number—be notified after print, when the ticket first has 10 or fewer ahead, and when it is called?
5. How can the developed system be evaluated in terms of functionality, usability, efficiency, reliability and accuracy, and user satisfaction through system testing, expert evaluation, and User Acceptance Testing (UAT)?



### 1.4 Objectives of the Study



#### 1.4.1 General Objective

The **general objective** of this study is to design, develop, and implement a **Standalone Kiosk-Based Queuing Management System with Thermal Printer** for the student service offices of the Philippine Electronics and Communication Institute of Technology (PECIT) to automate queue processing, organize service transactions, improve queue monitoring and coordination, and support efficient service delivery.

#### 1.4.2 Specific Objectives

Specifically, this study aims to:

1. Develop a standalone kiosk-based queuing management system that automates and organizes student transactions for the selected PECIT student service offices, particularly the Cashier, Registrar, and Data Management Office (DMO).
2. Implement a confirmation-based transaction process that allows clients to select a service, choose Regular or Priority queue status, type or tap and validate their Student ID or complete authorized kiosk-PIN walk-in issuance, and confirm the request before a queue number is generated.
3. Integrate a **58mm USB thermal printer** that automatically produces a physical queue ticket containing the necessary queue information after a confirmed transaction, and incorporate a priority queue mechanism that follows the **2 Priority → 1 Regular** scheduling rule while supporting multiple service windows assigned to the same service.
4. Implement a real-time queue display and monitoring feature that allows clients and authorized staff to view current queue information, supports coordination among service windows, and sends optional local GSM SMS notices with the ticket number after print, when a ticket first has 10 or fewer ahead in fair call order, and with the window name when the number is called. Students opt in with an SMS toggle that is off by default and uses the mobile number stored on their student record. Walk-in clients may enter a Philippine mobile only on the PIN overlay, and that text also includes the screen token.
5. Evaluate the developed system in terms of functionality, usability, efficiency, reliability and accuracy, and user satisfaction through system testing, expert evaluation, and User Acceptance Testing (UAT), using quality characteristics aligned with ISO/IEC 25010 [10].



### 1.5 Scope and Delimitation of the Study



#### 1.5.1 Scope

This study focuses on the design, development, and implementation of a Standalone Kiosk-Based Queuing Management System with Thermal Printer for selected student service offices of the Philippine Electronics and Communication Institute of Technology (PECIT), particularly the Cashier, Registrar, and Data Management Office (DMO).

The system provides a touchscreen-based kiosk that allows clients to:

- select the required student service;
- choose between Regular and Priority queue status;
- type their Student ID or tap an enrolled RFID/NFC student ID card during the confirmation stage, or issue a walk-in ticket after entering the kiosk PIN when a Student ID is not available;
- review and confirm the selected service and queue type;
- optionally turn on **Notify me by SMS** (off by default) if a mobile number is already stored on the student record; students do not type a number on Confirm;
- receive an automatically generated 58mm thermal queue ticket; and
- view estimated waiting time when sufficient historical and current queue information is available.

The system also includes staff-controlled queue management functions. Authorized staff can:

- view the waiting queue assigned to their service window;
- call the next queue;
- recall a previously called queue;
- place an active transaction on hold;
- resume a held transaction;
- complete an active transaction; and
- monitor an independent service timer for the assigned service window.

For services with multiple windows, the system supports a shared waiting queue across windows. The queue scheduler follows the 2 Priority → 1 Regular service order across windows sharing the same service.

The system further includes a real-time public queue display that shows current serving numbers and waiting queues. Student names and held tickets are not displayed publicly. A student who turns on SMS after a successful lookup may receive a GSM SMS at the mobile number stored on the student record: the ticket number after print, a come-back text the first time the ticket has 10 or fewer ahead in the same fair call order used by Call Next, and the window name when the number is called or recalled. A ticket already within 10 at print does not get the come-back text. If no mobile is on file, the SMS control stays off and disabled and shows that no SMS number is on file; print still proceeds. Walk-in clients may enter a Philippine mobile only on the PIN overlay. A walk-in ticket text also includes the screen token. Queue operations continue if the GSM module is unavailable. Queue and service activities are recorded in the local database for monitoring, history, and administrative reporting. The administrator can manage authorized users and student records, enroll RFID/NFC card serials (UID) and mobile numbers against Student IDs, set the kiosk walk-in PIN, monitor queue activities, review records, and access waiting-time and service-time reports based on completed transactions.

The system operates within the institution's Local Area Network (LAN) and does not require continuous internet connectivity for normal queue operations [22]. It uses Laravel 12, Blade, Vite/Tailwind, and MySQL/MariaDB on one mini PC that serves as both kiosk and application server (Intel Core i5 8th Generation, 8 GB RAM). The mini PC hosts the kiosk touchscreen, the 58mm XP-Q90EC printer, the ACR122U-A9 RFID/NFC reader, and the SIM7600G-H GSM/LTE module. When the printer is out of paper, the staff window and System Float show a paper-out alert, and checked staff or assistant numbers may receive one SMS.

#### 1.5.2 Delimitation

The study is limited to the selected student service offices of PECIT, specifically the Cashier, Registrar, and Data Management Office (DMO), and does not cover all institutional offices or departments.

The system is limited to local, LAN-based operation and does not include continuous cloud-based synchronization or remote internet-based administration as part of its current implementation.

The system does not include:

- online queue reservation;
- mobile application integration;
- a public cloud SMS or email gateway (notices use a local GSM/LTE module);
- cloud-based queue synchronization; and
- remote queue management through the internet.

RFID/NFC at the kiosk is used only to identify an enrolled student card at confirmation. It is not used for payment, attendance, or door access. Forgetting the physical ID is not a walk-in; the client may still type the Student ID. Students do not type a mobile number on Confirm. SMS uses the stored student mobile only when the SMS control is turned on. The kiosk does not show the full number; at most the last four digits may appear. SMS text contains the ticket number, the service or window, and, for a walk-in, the screen token. Names are not sent by SMS. The come-back text does not include an estimated wait.

Student tickets require a valid Student ID during the kiosk confirmation process, and only one open student ticket is allowed per Student ID per day. Walk-in tickets do not use a Student ID; they are issued at the kiosk after the walk-in PIN is accepted, or by an administrator, and more than one walk-in ticket may be open at the same time. The Guard role is used only as the kiosk issuer record and cannot log in. The student's name may be displayed during confirmation for verification and to authorized staff while serving, but it is not printed on the queue ticket, is not displayed on the public queue monitor, and is not included in SMS.

The evaluation of the system is limited to the selected participants involved in the study, including the intended users and one expert evaluator. Therefore, the findings represent the evaluation of the developed system within the study's selected environment and participants and should not be generalized to all educational institutions or queue management environments.

### 1.6 Significance of the Study

The **significance of the study** is presented from **macro to micro**: from technology in education, to Philippine higher education, to PECIT, then to the administrator, staff, students and parents, and future researchers.

#### 1.6.1 Educational Service Delivery

UNESCO states that technology in education should match the actual setting in which it will be used, and that it should be judged by relevance, equity, scalability, and sustainability [22]. The present study follows that principle. It applies a **standalone, LAN-based** queue design rather than a cloud-only model, so student-service work can continue when the internet is down.

#### 1.6.2 Philippine Higher Education

Queueing is a known concern in Philippine higher education during enrollment, payment, and document-request periods [5], [7], [16]. This study adds a localized campus-office model that other institutions may compare with web-based, appointment-based, or ticketless designs.

#### 1.6.3 Philippine Electronics and Communication Institute of Technology (PECIT)

The study provides **PECIT** with a queue management solution tailored to its Cashier, Registrar, and DMO. Its standalone LAN-based design allows essential queue operations to function on the local network without continuous internet connectivity. The kiosk, staff workstations, public display, administrator functions, and local database operate as **one** institutional environment.

#### 1.6.4 System Administrator

The system gives the administrator centralized functions for monitoring queue activities, managing authorized users and student records, enrolling RFID/NFC card serials (UID) and mobile numbers against Student IDs, setting the kiosk walk-in PIN, storing staff mobiles for the printer paper-out text, reviewing transaction records, and generating reports. Recorded queue and service information can support the monitoring of waiting time, service time, completed transactions, and service-window performance. Reports are based on **done** tickets only.

#### 1.6.5 Administrative Staff

The system assists Cashier, Registrar, and DMO personnel through a structured queue process. Authorized staff can **Call Next**, **Recall**, **Hold**, **Call Held**, and **Complete**. Staff can see the student name while serving a validated student ticket, or the walk-in reason and screen token for an authorized walk-in ticket. For services with multiple windows, the system supports a shared waiting queue while keeping independent active transactions and service timers. The service-wide **2 Priority → 1 Regular** rule provides a consistent method for managing queue order.

#### 1.6.6 Students and Parents

The system provides students and parents with a more organized and transparent way of joining and monitoring queues. Through the kiosk, they can select the service, choose Regular or Priority, type or tap and validate their Student ID or complete authorized walk-in issuance, optionally turn on SMS (off by default), confirm the request, and receive a printed **number-only** ticket. Students do not type a phone number; SMS uses the number stored with the Student ID. Walk-in clients may leave the mobile blank or enter a Philippine number after the kiosk PIN is accepted, and they see a screen token on the cooldown screen. The public display allows them to monitor queue progress without remaining in a physical line. The system may show an estimated waiting time when data are sufficient, and may send **GSM SMS** notices after print, when the ticket first has 10 or fewer ahead, and when it is called.

#### 1.6.7 Future Researchers

The study may serve as a reference for future researchers developing queue management systems, self-service kiosks, and localized information systems for educational institutions [9]. It provides a basis for further work on additional offices, alternative scheduling, mobile or online queueing, advanced analytics, and other extensions of the current system.

### 1.7 Definition of Terms

To ensure clarity and consistency, the following terms are defined conceptually and operationally based on their use in the study and the developed system.

**Administrative Staff** – Refers to authorized personnel assigned to the Cashier, Registrar, and Data Management Office (DMO) who process student transactions and use the staff interface to manage queues.

**Call Held** – Refers to the staff function used to resume a previously held queue ticket at a service window. A held ticket can be resumed without following the normal **2 Priority → 1 Regular** calling sequence.

**Cashier** – Refers to the PECIT student service office responsible for processing payment-related and other financial transactions.

**Data Management Office (DMO)** – Refers to the PECIT office responsible for handling student data-related services, including record verification and other related transactions.

**Estimated Waiting Time** – Refers to the calculated approximate time a client may wait before being served, based on available current queue information and sufficient historical service data.

**GSM SMS Notice** – Refers to an optional **number-only** text sent through a SIM7600G-H GSM/LTE USB module on the mini PC that also hosts the kiosk and application server. A student turns on **Notify me by SMS** at confirmation (off by default). The notice uses the mobile number stored on the student record, not a number typed at the kiosk. If no mobile is on file, the control stays off and print still proceeds. A walk-in ticket may use a blank mobile or a Philippine number (`09` and 9 more digits) entered only on the PIN overlay. The sender on the kiosk PC finds the modem command port and includes the Smart message center in each text. A ticket that opted in may receive the ticket number after print, a come-back notice the first time it has 10 or fewer ahead in fair call order, and the window name when the number is called or recalled. A ticket already within 10 at print does not get the come-back notice. A walk-in ticket text also includes the screen token. Queue operations do not depend on the modem.

**Interview** – Refers to the oral data-gathering method used only with Cashier, Registrar, and DMO staff during requirements analysis. Students and parents were not interviewed.

**Kiosk PIN** – Refers to the walk-in access code stored in the system settings and edited by the administrator. Every use of the kiosk walk-in control requires this PIN. The PIN is checked on the server, and the Guard role cannot log in to the system.

**Local Area Network (LAN)** – Refers to the local network used by the mini PC (kiosk and server), staff workstations, public display, administrator computer, and database to communicate within the PECIT environment without requiring continuous internet connectivity.

**Mini PC** – Refers to the Intel Core i5 8th Generation computer with 8 GB RAM that serves as both the public kiosk host and the application server.

**Multi-Counter Queue** – Refers to a service configuration in which multiple service windows share a common waiting queue while each window maintains its own active transaction and service timer. This is applied to the Cashier service in the current system.

**Notify me by SMS** – Refers to the confirmation-step control that is **off by default**. Students do not type a phone number. If a mobile number is stored with the Student ID, turning the control on copies that number to the ticket for GSM notices. The kiosk does not show the full number; at most the last four digits may appear after the control is on. If no mobile is on file, the control stays off and disabled and shows **No SMS number on file**. Confirm and print still proceed.

**Paper-Out Alert** – Refers to the notice shown on the staff window and System Float when the 58mm thermal printer has no paper or a ticket stays stuck in Printing. Stored tickets remain valid. The administrator may store staff or assistant mobiles and check Send SMS for each number that should receive one text when the paper first runs out. Loading a new roll clears that mark.

**Priority Queue** – Refers to the queue category assigned to qualified priority clients and processed according to the system's **2 Priority → 1 Regular** scheduling rule.

**Public Queue Display** – Refers to the display component that shows the current serving queue numbers and waiting queue information to clients without displaying student names.

**Queue Management System (QMS)** – Refers to the integrated hardware and software system developed to generate, organize, monitor, and manage student service queues at PECIT.

**Queue Number** – Refers to the identifier automatically generated by the system for a confirmed service request and used to determine the client's position in the appropriate service queue.

**Questionnaire (Needs-Assessment Survey)** – Refers to the student-and-parent form used before the system was built. Part I collected profile data. Part II used a five-point Likert scale (quantitative). Part III used checklist items with an optional Other line. Part IV collected open suggestions (qualitative). Those results belong in Chapter 3 and are not mixed with Chapter 4 evaluation scores.

**Recall** – Refers to the staff function used to re-announce a previously called queue number when necessary.

**Registrar** – Refers to the PECIT student service office responsible for academic records, enrollment-related documents, certifications, clearances, and other registrar services.

**Regular Queue** – Refers to the standard queue category for clients who are not classified under the system's Priority category.

**RFID/NFC Confirmation** – Refers to tapping an enrolled ISO 14443A / MIFARE student ID card on the ACR122U-A9 kiosk reader during the third kiosk step. The card serial is mapped to a Student ID. Service and Regular or Priority are still selected before confirm. Typing the Student ID remains available when the card is not presented.

**Service Window** – Refers to a designated staff workstation or counter assigned to a particular service and used to process queue transactions.

**Standalone System** – Refers to the queue management system designed to perform its essential operations within the institution's local computing and network environment without requiring continuous internet connectivity.

**Student ID** – Refers to the identification number typed by the client during the kiosk confirmation stage, or resolved from an enrolled RFID/NFC card serial, and validated against the authorized student list before a student queue ticket is generated. Walk-in tickets do not use a Student ID.

**Thermal Printer** – Refers to the 58mm USB XP-58 (XP-Q90EC) printer connected to the mini PC and used to automatically produce the client's physical queue ticket after confirmation. Paper-out is reported on the staff window and System Float, with an optional SMS to checked staff numbers.

**User Acceptance Testing (UAT)** – Refers to the evaluation conducted after the system was implemented, using the Cashier, Registrar, DMO, and Student System Feedback Forms, to determine whether the developed queue management system satisfies functionality, usability, efficiency, reliability and accuracy, and satisfaction requirements, using quality characteristics aligned with ISO/IEC 25010 [10]. UAT results belong in Chapter 4 and are separate from the Chapter 3 needs-assessment questionnaire.

**Walk-in Ticket** – Refers to a queue ticket issued without a Student ID after the kiosk walk-in PIN is accepted, or by an administrator. Walk-in tickets may be open at the same time, are printed as numbers only, and appear to staff as Walk-in followed by the recorded reason and a screen token. The walk-in overlay may accept a blank mobile or a Philippine mobile (`09` and 9 more digits). Any other number keeps Issue and Print disabled. Each walk-in receives a screen token such as T1006ABC: weekday initial, month, day, and three random letters. The token is shown on the cooldown screen and to staff while serving, is included in the ticket SMS when a mobile was entered, and is not printed or shown on the public display.



# CHAPTER 2



## REVIEW OF RELATED LITERATURE AND STUDIES

This chapter presents the review of related literature and studies relevant to the development of the Standalone Kiosk-Based Queuing Management System with Thermal Printer for Student Service Offices at the Philippine Electronics and Communication Institute of Technology (PECIT). The review covers queue management systems, self-service kiosk technology, waiting-time information, thermal ticket printing, standalone and Local Area Network (LAN)-based information systems, and the application of digital technologies in educational institutions. Foreign and local studies published from **2021 to 2026** are reviewed to establish the basis of the proposed system and to support the **research gap** stated in **Chapter 1**.

The reviewed literature and studies establish that technology can support more organized service processes, improve access to queue information, and assist personnel in managing service transactions. However, existing systems differ in their architecture, service coverage, queueing mechanisms, ticketing methods, and integration of kiosk, staff, display, and administrative functions. These differences provide a basis for developing a localized system that responds to PECIT’s present process of manual first-come-first-served lining in the Cashier, Registrar, and Data Management Office (DMO).

### 2.1 Related Literature



#### 2.1.1 Queue Management Systems

Queue management systems are designed to organize the order in which clients receive services and to provide information about queue progress. In traditional service environments, clients commonly wait in physical lines and depend on verbal announcements or other manual methods to determine their turn. Such arrangements become difficult to manage when the number of clients increases, particularly when several service points operate at the same time [12].

Tesa et al. [21] reviewed digital queueing systems and reported that online queues, self-service kiosks, and reservation systems can reduce waiting time and improve the client experience. They also noted that the effectiveness of digital queueing depends on local infrastructure and users’ ability to use the technology. This finding is relevant to PECIT because the present process is still a manual first-come-first-served line. A computerized queue can provide a clearer alternative, but it must still match the institution’s local setting.

This literature supports the present study because PECIT’s student service offices are physical service environments in which clients wait to be served. The proposed system applies computerized queue generation, queue monitoring, and public queue information as a structured alternative to manually managed first-come-first-served lining.

#### 2.1.2 Self-Service Kiosk Technology

Self-service kiosks allow clients to complete the first steps of a service transaction through a technological interface [21]. They are useful where many users select a service or start a transaction in the same way [21], [12]. A kiosk can move routine entry work away from staff while keeping a standard process for joining the queue [12].

For the present study, the kiosk is the main client entry point. The PECIT kiosk allows a client to select a service, choose Regular or Priority, type or tap and validate a Student ID, confirm the request, and obtain a printed queue ticket. Clients who cannot use a Student ID may request a walk-in ticket at the same kiosk after entering the designated kiosk PIN. The student flow remains three steps: service selection, priority selection, and confirmation. Recent campus systems likewise use RFID or NFC at a reader so a student can tap instead of typing an ID [1], [3].

#### 2.1.3 Waiting-Time Information in Queue Management

Waiting time is an important concern in service systems because clients need information not only about their queue number but also about how long they may wait. Tesa et al. [21] reported that digital queue systems can reduce waiting-related problems, but only when the supporting data and infrastructure are adequate. Pantonial et al. [16] likewise found that queue conditions and monitoring are closely related to cashier efficiency in a Philippine higher education setting. The present study records wait and service times from completed tickets for reports and estimates; it does not claim a measured cut in waiting time.

This concept is relevant because the PECIT system may show an estimated waiting time during confirmation. The estimate is shown only when enough completed-service history and current waiting and serving information are available. The system therefore treats estimated waiting time as supporting information, not as a guaranteed prediction.

#### 2.1.4 Thermal Printing Technology

A physical queue ticket gives the client a clear reference that can be kept while waiting, instead of depending only on a place in a first-come-first-served line [12], [8]. Queue tickets usually contain limited information such as the queue number, service, and date or time.

Other Philippine systems have used a ticketless Student ID approach with SMS and voice announcement [17]. The present study takes a different route. After confirmation, the PECIT kiosk sends ticket information to a connected 58mm USB thermal printer. The printed ticket shows the queue number and may include an estimated waiting time when enough history exists. The student’s name and the walk-in screen token are not printed.

#### 2.1.5 Standalone and Local Area Network-Based Information Systems

Information systems should match the technical setting in which they will be used. Where continuous internet access is not required for daily work, a local system can be more suitable than a cloud-dependent platform [22], [11].

A Local Area Network (LAN) allows computers and devices within a limited area to exchange information. For an institutional queue management system, a local network can connect the kiosk, staff workstations, public display, administrator computer, and database.

UNESCO [22] emphasizes that technology in education should be judged by relevance, equity, scalability, and sustainability, and that it should respond to actual institutional needs. This principle supports PECIT’s standalone, LAN-based design. The system uses Laravel 12, Blade, Vite/Tailwind, and MySQL/MariaDB, and its essential queue operations do not require continuous internet connectivity.

#### 2.1.6 Digital Technology in Educational Administration

Educational institutions increasingly use information systems to support administrative processes, data management, and student services. UNESCO [22] identifies system management as one area in which digital technology can help, provided the technology is appropriate to the institution’s context.

This principle applies to student service offices because these offices handle repeated transactions for many students and other clients. A queue management system is a focused use of information technology for queue entry, service order, monitoring, and transaction records.

#### 2.1.7 Queue Management in Philippine Higher Education

Queueing concerns are especially relevant in Philippine higher education because administrative offices may face high volume during enrollment, payment periods, document requests, and related services.

Pantonial et al. [16] investigated student payment behaviors, queue management, and cashier efficiency at Liceo de Cagayan University, using surveys and observations of 761 students. Their study shows that queue conditions affect cashier operations and the student transaction experience. Maala et al. [12] likewise described manual campus queueing as a source of delay, unclear order, and extra work for staff.

These studies are relevant because the Cashier is one of PECIT’s target offices, and the present process is still manual first-come-first-served lining. The present study goes further by developing an integrated system that generates tickets, coordinates staff windows, and records completed transactions.

#### 2.1.8 Digital Transformation in Philippine Higher Education

Digital transformation is also part of administrative improvement in Philippine higher education. CHED Regional Office XI [5] documented digital-transformation work that includes student services and a queueing system with online appointment and automated printing.

This context shows that queue-related digital services are already recognized in Philippine higher education. It also shows that institutions adopt different models according to their processes and infrastructure. The present study therefore develops a solution for PECIT’s on-site, LAN-based offices rather than copying an online appointment model.

#### 2.1.9 Localized Information Systems for Philippine Educational Institutions

Grepon et al. [9] designed and implemented an e-School system for a community college in Northern Mindanao. The study addressed admissions, data processing, storage, retrieval, and reporting, and it emphasized systems built around the actual requirements of a Philippine educational institution.

The study does not focus on queue management, but it supports the principle of localized information-system development. The present study applies that principle to student service queues at PECIT.

### 2.2 Related Studies



#### 2.2.1 Foreign Studies



##### 2.2.1.1 Digital Queueing Systems and Self-Service Kiosks

Tesa et al. [21] conducted a systematic literature review of digital queueing systems in Indonesian banking services. The review examined online queues, self-service kiosks, and reservation systems. The authors reported that digital queue systems can improve the customer experience and reduce waiting-related problems, but that results depend on infrastructure and users’ ability to use the technology.

The study is relevant because it supports kiosk-based and digital entry into a physical service process. Its setting, however, is banking, and many of the reviewed systems rely on online or reservation features. PECIT’s system is designed as a local, standalone LAN-based platform for student service offices, with a printed thermal ticket rather than an internet-dependent reservation.

##### 2.2.1.2 Waiting-Time Information in Service Systems

Tesa et al. [21] treated reduced waiting time as a main expected benefit of digital queueing, while warning that weak data or infrastructure can limit that benefit. This supports the inclusion of estimated waiting time in the PECIT system and the rule that an estimate is shown only when enough completed history and current queue information are available.

The PECIT estimate also accounts for multiple windows that share one service, such as Cashier 1 to Cashier 3. This differs from a single-line first-come-first-served wait, in which clients have no computed estimate and no public display of who is next.

##### 2.2.1.3 Local and Offline Information Systems

Linhalis and Silva [11] reviewed ICT solutions used in settings with low or unstable internet connectivity and noted that some systems operate through a local network rather than continuous online access. Although their review focused on education platforms, the same design idea applies to institutional service systems that must keep running inside a campus LAN.

The study supports PECIT’s standalone architecture. It does not address thermal tickets, Student ID confirmation, or multi-counter priority scheduling, which remain part of the present system’s localized design.

##### 2.2.1.4 RFID/NFC Campus Identification

Akila, Pratheek, and Poonia [1] implemented a campus smart ID in which an RFID reader scans the card and identity data stay on a server, not on the card. NFC tap-login was also part of that design. The present study uses RFID/NFC only at kiosk confirmation: the card serial maps to a Student ID. It is not used for payment, attendance, or door access.

Ariningsih and Muhammad [2] evaluated a campus ticketing application with ISO/IEC 25010:2023 and a Likert-scale questionnaire. That supports the present study’s ISO/IEC 25010-aligned UAT, even though their ticketing system was an IT helpdesk tool rather than a student-service queue.

#### 2.2.2 Local Studies



##### 2.2.2.1 Automated Enrollment Queueing System

Espinosa et al. [7] developed an automated enrollment queueing system to address student waiting lines, overcrowding, and staff workload. The authors reported that an automated queue could provide a more controlled process for students and guardians and reduce the need for loud manual announcements.

The study is closely related because both projects address queueing in a Philippine higher education institution. It focuses mainly on enrollment, whereas the PECIT system covers the Cashier, Registrar, and DMO. The PECIT system also differs through LAN-based operation, 58mm thermal printing, Student ID validation, kiosk-PIN walk-in issuance, priority scheduling, and multi-counter support.

##### 2.2.2.2 Queue Management and Cashier Efficiency

Pantonial et al. [16] examined student payment behaviors, queue management, and cashier efficiency at Liceo de Cagayan University. Using surveys and observations of 761 students, they found that queue strategies and monitoring were related to cashier performance.

The study supports the relevance of queue management to Cashier operations. It investigated relationships among behavior, queues, and efficiency rather than building a complete queue platform. The present study addresses that implementation gap by generating tickets, coordinating several cashier windows, and recording completed service.

##### 2.2.2.3 Ticketless ID-Operated Queue Management

Ramoga [17] developed a ticketless, Student ID–operated queue management system with SMS notification and voice-over for the Registrar and Cashier offices of Jose Rizal Memorial State University–Dipolog Campus. The study addressed long waiting times, crowded service areas, and communication gaps in a manual process.

The main difference is the queueing approach. Ramoga’s system is ticketless and uses SMS and voice announcement. The PECIT system issues a physical 58mm thermal ticket, keeps names off the ticket, public display, and SMS, and operates within a standalone LAN. SMS in the present study is optional and local (GSM/LTE module): a ticket-number text after print, a come-back text at 10 or fewer ahead, and a window-name text when called. Students opt in with **Notify me by SMS** (off by default); the notice uses the mobile number stored on the student record rather than a number typed at the kiosk. Walk-in clients who have no Student ID are handled through a kiosk PIN and may enter a Philippine mobile only on that overlay. The issued text includes a screen token that is shown on the cooldown screen and is not printed. It is not a cloud SMS gateway and does not replace the printed ticket.

##### 2.2.2.4 Smart Queuing and Appointment Management System

Concepcion et al. [6] developed a Smart Queuing and Appointment Management System for client services at Bulacan State University–Bustos Campus. The web-based system combined walk-in queueing and online appointment scheduling with real-time monitoring. It was built with HTML, CSS, JavaScript, PHP, and MySQL and was evaluated using the Technology Acceptance Model and ISO/IEC 25010:2023, with a general weighted mean of 4.59.

The study is highly relevant because it addresses client-service queue management in another Philippine higher education institution. Its web-based and appointment-oriented design differs from the PECIT system, which emphasizes standalone LAN operation, physical thermal tickets, Student ID confirmation, kiosk-PIN walk-in issuance, 2 Priority → 1 Regular scheduling, shared multi-counter queues, and staff Hold and Call Held functions. Concepcion et al. evaluated acceptance with the Technology Acceptance Model and ISO/IEC 25010 [6], [10]. The present study uses ISO/IEC 25010-aligned UAT rather than TAM scales.

##### 2.2.2.5 Localized School Information System

Grepon et al. [9] developed an e-School information system for a community college in Northern Mindanao. The study showed how a localized information system can support institutional processing, storage, retrieval, and reporting.

Although it does not address queue management, it supports institution-specific system development in a Philippine educational setting. The present study applies that localized approach to student service queues.

##### 2.2.2.6 Campus Queuing Management System

Maala et al. [12] developed a queuing management system for Manuel S. Enverga University Foundation Candelaria Inc. They described traditional campus queueing as error-prone because of manual processes, weak real-time communication, and fragmented data. Their proposed system aimed to digitize queue joining, monitoring, and reporting.

The study supports the need to replace informal physical lining with a recorded queue. PECIT’s present process is likewise manual first-come-first-served lining. The present system differs by using an on-site kiosk and 58mm thermal printer, validating Student ID or issuing a PIN-authorized walk-in ticket, and applying a shared 2 Priority → 1 Regular rule across multiple windows.

##### 2.2.2.7 Kiosk Queue with Printed Slip and Public Display

Mallari et al. [13] developed CLIQUE for the Office of the University Registrar at Angeles University Foundation. Clients used a kiosk-like touchscreen, received a printed queue slip, and could follow the queue on a display. The system was web-based (ASP.NET, C#, and MySQL) and also allowed browser tracking.

The study is relevant because it combines a kiosk, a printed slip, a public display, and staff monitoring in a Philippine registrar office. PECIT’s system is standalone on a LAN, uses 58mm thermal printing, confirms Student ID or a kiosk-PIN walk-in, and applies 2 Priority → 1 Regular across shared windows rather than a registrar-only web kiosk.

##### 2.2.2.8 Enrollment Queuing in Another Philippine College

Caranza et al. [4] described a smart enrollment queuing system for St. Clare College of Caloocan. Manual enrollment queues for requirements, cashier payment, and records checking produced delay and crowding. The proposed system aimed to help staff manage queues and give students a clearer process.

The study supports the need to organize campus service lines. It is enrollment-focused. The PECIT system covers Cashier, Registrar, and DMO with Student ID confirmation, walk-in PIN issuance, thermal tickets, and shared multi-window scheduling.

##### 2.2.2.9 Kiosk and Thermal Printer in a Service Office

Falcon et al. [8] implemented a three-tier queue for land-transportation clients: a touchscreen kiosk with a thermal printer, a personnel workstation, and a public display. They chose thermal printing for speed and low maintenance and cleared kiosk data for privacy.

The hardware pattern is close to PECIT’s kiosk, 58mm printer, staff panel, and display. The setting is a government transport office, not a campus LAN with Student ID confirmation, walk-in PIN, and 2 Priority → 1 Regular shared cashier windows.

##### 2.2.2.10 Self-Service Kiosk with RFID and Thermal Printer

Barbado et al. [3] developed ADUPay, a self-service payment kiosk at Adamson University with an RFID reader and a thermal printer. The study is relevant because it combines campus kiosk hardware, RFID, and thermal printing. It is a payment kiosk for student organizations, not a queue manager for Cashier, Registrar, and DMO. PECIT uses RFID only to confirm an enrolled Student ID and prints a number-only queue ticket, not a payment receipt.

##### 2.2.2.11 Paperless Digital Queue for Cashier and Records

Sumalinog and Mahusay [20] proposed Q-Line for the University of Cebu – Lapu-Lapu and Mandaue. Students get queue numbers from kiosks or an online interface, follow FIFO order, and receive alerts instead of printed priority numbers. The study supports digital queueing for cashier and records offices. PECIT keeps a 58mm printed ticket as the client’s physical reference and uses optional GSM SMS in addition to the ticket, not as a paperless replacement.

##### 2.2.2.12 Web Appointment and Queue Monitoring for Registrar and Cashier

Panganiban and Becenia [15] developed iSkulFlow for registrar and financial services at VMC Asian College Foundation, Inc. The system combines appointment booking, document requests, email notices, and a web administration panel. It is web-based and appointment-oriented. PECIT remains an on-site kiosk with thermal tickets, Student ID or PIN walk-in confirmation, and LAN operation without online appointment.

### 2.3 Synthesis

The reviewed literature and studies provide a consistent foundation for the proposed queue management system.

First, digital queueing research shows that technology can structure physical waiting and give clients a clearer way to enter and monitor a queue [21], [12]. This is directly relevant to PECIT’s current first-come-first-served lines.

Second, self-service kiosks can standardize the first steps of a transaction. The PECIT kiosk applies this through service selection, Regular or Priority selection, Student ID validation by keypad or RFID/NFC tap or kiosk-PIN walk-in issuance, confirmation, and ticket generation.

Third, waiting-time information can help clients, but only when the supporting data are sufficient [21], [16]. The PECIT system therefore shows an estimate only when enough completed history is available.

Fourth, a printed ticket provides a physical queue reference. The present study uses a 58mm USB thermal printer and does not print the student’s name, in contrast to ticketless ID-and-SMS designs [17]. Optional GSM SMS in the PECIT system supplements the ticket; it does not replace it.

Fifth, Philippine studies establish that queue management matters in higher education. Espinosa et al. [7] addressed enrollment queues, Pantonial et al. [16] examined cashier queues, Ramoga [17] addressed Registrar and Cashier offices, Concepcion et al. [6] combined walk-in and online appointment queueing, Mallari et al. [13] used a registrar kiosk with a printed slip and display, Caranza et al. [4] addressed enrollment queues, Maala et al. [12] digitized campus queue monitoring, Barbado et al. [3] used RFID and thermal printing at a campus kiosk, Sumalinog and Mahusay [20] proposed a paperless cashier and records queue, and Panganiban and Becenia [15] combined registrar and cashier appointments on the web.

Sixth, those systems use different architectures. Some are web-based and appointment-oriented, some are paperless or ticketless, and others use SMS. The present study uses physical thermal tickets, a standalone LAN, RFID/NFC confirmation as an optional input on the third kiosk step [1], [3], and local GSM SMS rather than a public cloud messaging API.

Finally, technology should match institutional requirements and infrastructure [22], [11], [9]. This supports a localized PECIT design.

The proposed system therefore combines a touchscreen kiosk, Student ID validation by keypad or RFID/NFC tap, authorized kiosk-PIN walk-in issuance, Regular and Priority queueing, confirmation before ticket generation, 58mm thermal printing, estimated waiting time when data are sufficient, optional local GSM SMS after **Notify me by SMS** is turned on (stored student mobile; walk-in Philippine number and screen token), service-wide 2 Priority → 1 Regular scheduling, shared multi-counter queues, Hold and Call Held staff controls, a number-only public display, local database storage, and administrative reports based on completed transactions only.

### 2.4 Summary of the Review

The literature in this chapter supports the **research gap** already stated in **Chapter 1**. That gap is not restated here.



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

The system requirements include service selection; Regular or Priority selection; confirmation before ticket generation; Student ID validation by keypad or RFID/NFC tap or authorized kiosk-PIN walk-in issuance; one open student ticket per Student ID per day; queue-number generation; 58mm thermal printing; estimated waiting time when sufficient completed history is available; optional local GSM SMS when a student turns on **Notify me by SMS** (off by default; stored student mobile) or when a walk-in enters a Philippine number on the PIN overlay, sent after print, when a ticket first has 10 or fewer ahead, and when it is called; shared service queues; 2 Priority → 1 Regular scheduling; multi-counter support; staff Call Next, Recall, Complete, Hold, and Call Held; number-only public display; transaction recording; administrator user, student, card UID, mobile, PIN, paper SMS contacts, and report functions; authentication; and standalone LAN operation.

The student kiosk remains a three-step flow: service selection, priority selection, and confirmation. During confirmation, the Student ID is typed or read from an enrolled RFID/NFC card and validated against the authorized student list. Clients who do not bring the card may still type the Student ID. Clients who cannot use a Student ID may request a walk-in ticket at the same kiosk after the walk-in PIN is accepted. Cashier 1, Cashier 2, and Cashier 3 share one Cashier waiting queue while keeping independent active transactions and service timers.

#### 3.1.2 Process

*Figure 3.2. Waterfall Software Development Model Used in the Study*

The process stage uses five sequential Waterfall phases.

##### 3.1.2.1 Requirements Analysis

This phase identified the problems of the existing first-come-first-served line and determined the functional, non-functional, and operational requirements of students, parents, Cashier, Registrar, and DMO personnel, and the administrator. Questionnaires, interviews, observation, and document review were used.

##### 3.1.2.2 System Design

This phase converted the requirements into the architecture, process models, database structure, workflow, and interface designs. The design covers the mini PC (kiosk and server), 58mm printer, ACR122U-A9 RFID/NFC reader, SIM7600G-H GSM/LTE module, staff panels, public display, and administrator dashboard, including walk-in PIN issuance, fair scheduling, Hold and Call Held, paper-out alerts on the staff window and System Float, with optional staff SMS, optional SMS notices, and privacy rules.

##### 3.1.2.3 Implementation/Development

This phase produced the working application: Student Kiosk Module, Staff Module, Administrator Module, Queue Display Module, local database, thermal printer integration, RFID/NFC confirmation, and GSM SMS sending. Implementation includes Student ID validation by keypad or tap, kiosk-PIN walk-in issuance, queue generation, thermal printing, shared-queue processing, priority scheduling, staff controls, and transaction recording.

##### 3.1.2.4 Testing

Testing verified the kiosk, Student ID keypad and RFID/NFC tap, walk-in rules, one-open-student-ticket-per-day rule, queue generation, 58mm printing, paper-out alert on the staff window and System Float, GSM SMS ticket, 10-away, and called notices, fair scheduling, staff functions, public display, multi-counter behavior, database transactions, and access controls. Integration testing verified communication among the mini PC (kiosk and server), database, printer, ACR122U-A9 reader, SIM7600G-H module, staff module, display, and administrator module.

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

Authorized staff can Call Next, Recall, Complete, Hold, and Call Held. Windows that share a service use one waiting queue and keep independent active calls and timers. Staff may see the student name while serving a validated student ticket, or Walk-in (reason) and the screen token for an authorized walk-in ticket.

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


| Result                              | Instrument                                                 | When gathered        | Where reported                    |
| ----------------------------------- | ---------------------------------------------------------- | -------------------- | --------------------------------- |
| Needs-assessment survey             | Student-and-parent questionnaire (Parts I–IV)              | Before development   | Chapter 3, Tables 3.1–3.3         |
| Staff interview                     | Oral interview with Cashier, Registrar, and DMO personnel  | Before development   | Chapter 3, Section 3.2            |
| Functional and non-functional tests | Test cases on the implemented LAN system                   | After implementation | Chapter 4, Sections 4.7.1–4.7.2   |
| Expert evaluation                   | Technical Expert System Evaluation Form                    | After implementation | Chapter 4, Table 4.4              |
| User acceptance                     | Cashier, Registrar, DMO, and Student System Feedback Forms | After implementation | Chapter 4, Tables 4.3 and 4.5–4.9 |


**Respondent profile**


| Respondent | Frequency | Percentage |
| ---------- | --------- | ---------- |
| Students   | 40        | 80%        |
| Parents    | 10        | 20%        |
| Total      | 50        | 100%       |


Fifty respondents completed the form: 40 students (80%) and 10 parents (20%).


| Rating | Interpretation    |
| ------ | ----------------- |
| 5      | Strongly Agree    |
| 4      | Agree             |
| 3      | Neutral           |
| 2      | Disagree          |
| 1      | Strongly Disagree |


Interview. Interviews with Cashier, Registrar, and DMO personnel covered existing first-come-first-served procedures, operational difficulties, priority handling, and needed staff functions.

Observation. The researchers observed client movement, line formation, waiting, and staff calling practices.

Document Review. Existing procedures and related records were reviewed to determine the information needed for ticket generation, staff serving, and reporting.

#### 3.2.3 Existing System Analysis

The current process is a manual first-come-first-served line that uses no queue ticket. Likert-scale results from the 50 respondents were analyzed using the weighted mean:

WM = Σfx / N

where Σfx is the sum of the products of frequency and rating, and N is the number of responses.


| Weighted Mean Range | Interpretation    |
| ------------------- | ----------------- |
| 4.21–5.00           | Strongly Agree    |
| 3.41–4.20           | Agree             |
| 2.61–3.40           | Neutral           |
| 1.81–2.60           | Disagree          |
| 1.00–1.80           | Strongly Disagree |


**Table 3.1 Current Queuing Experience**


| Questionnaire Statement                                    | Weighted Mean | Interpretation |
| ---------------------------------------------------------- | ------------- | -------------- |
| 1. The current queuing system is well organized.           | 2.43          | Disagree       |
| 2. The waiting time before being served is reasonable.     | 2.30          | Disagree       |
| 3. I experience confusion during the queuing process.      | 4.17          | Agree          |
| 4. The current queuing system meets my needs as a student. | 2.47          | Disagree       |
| 5. The current queuing process needs improvement.          | 4.40          | Strongly Agree |
| Overall Mean                                               | 3.15          | Neutral        |


The overall mean of 3.15 is Neutral, but the item means show why the study was conducted. Respondents strongly agreed that the current process needs improvement (4.40) and agreed that they experience confusion (4.17). They disagreed that the line is well organized (2.43), that waiting time is reasonable (2.30), and that the process meets their needs (2.47).

Part III of the student-and-parent questionnaire (checklist items, not an interview) was summarized by the most checked box on each item:

**Table 3.2 Most Checked Responses from the Student and Parent Questionnaire (Part III)**


| Item                         | Most checked response                                                                    |
| ---------------------------- | ---------------------------------------------------------------------------------------- |
| 1. Experience in line        | The lines are usually long, especially during peak hours such as enrollment and payment. |
| 2. Problems encountered      | Some people cut in line or do not follow proper order.                                   |
| 3. How a kiosk could help    | It can organize the queue and prevent people from cutting in line.                       |
| 4. Printed ticket or display | Yes, because I can monitor my turn through the display screen.                           |
| 5. Recommended improvement   | Installation of a kiosk for automatic queue number generation.                           |


These most-checked answers match the problem and the proposed system: the current first-come-first-served line is long, uses no ticket, and is subject to line cutting, and respondents want a numbered kiosk queue with a public display.

These findings support Specific Problems 1 and 4: the current first-come-first-served line uses no ticket, is subject to line cutting, and has no real-time display of who is being served and who is waiting.

#### 3.2.4 Requirements Gathering

**Table 3.3 Perception of the Proposed Kiosk-Based Queuing System**


| Questionnaire Statement                                                              | Weighted Mean | Interpretation |
| ------------------------------------------------------------------------------------ | ------------- | -------------- |
| 1. A kiosk-based automated queuing system would improve service efficiency.          | 4.53          | Strongly Agree |
| 2. A printed queue ticket would help me track my turn easily.                        | 4.60          | Strongly Agree |
| 3. A real-time queue display would make the system more organized.                   | 4.57          | Strongly Agree |
| 4. Selecting services (Cashier, Registrar, DMO) through a kiosk would be convenient. | 4.50          | Strongly Agree |
| 5. A priority queue option (regular/priority) would improve service flexibility.     | 4.37          | Strongly Agree |
| 6. A confirmation step before ticket generation would help reduce errors.            | 4.43          | Strongly Agree |
| 7. I am willing to use a kiosk-based queuing system.                                 | 4.63          | Strongly Agree |
| Overall Mean                                                                         | 4.52          | Strongly Agree |


All seven proposed-system items were interpreted as Strongly Agree. Respondents were willing to use a kiosk (4.63). They also strongly agreed that a printed queue ticket (4.60), a real-time display (4.57), kiosk service selection (4.50), a confirmation step (4.43), and a Regular/Priority option (4.37) would improve the process. The overall mean of 4.52 is Strongly Agree. These ratings support the features built into the standalone kiosk system.

Part IV suggestions matched those features: install a kiosk that issues a queue number; print a ticket as a clear queue reference so clients need not remain in a physical line; show Now Serving and waiting numbers on a public display; let clients select Cashier, Registrar, or DMO at the kiosk; provide Regular and Priority; and confirm the request before the ticket is printed. SMS alerts requested in the needs assessment were implemented through a local GSM/LTE module rather than a cloud API. Suggestions such as extra counters only at peak hours remain later work.

**Table 3.3 Requirements Derived from the Data**


| Finding                                 | Corresponding System Requirement                                                                                                       |
| --------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------- |
| Need for better queue organization      | Automated queue generation instead of a physical first-come-first-served line                                                          |
| Waiting-time concerns                   | Organized queue flow, ETA when history is sufficient, and queue monitoring                                                             |
| Confusion during queuing                | Queue numbers, printed tickets, and a public display                                                                                   |
| Long lines and overcrowding             | Kiosk-based issuance so clients need not remain in a physical line                                                                     |
| Need for a clear queue reference        | Printed 58mm thermal ticket, number only                                                                                               |
| Need to monitor the queue while waiting | Real-time public display without names or held tickets                                                                                 |
| Need for service selection              | Cashier, Registrar, and DMO on the public kiosk                                                                                        |
| Need for priority handling              | Regular/Priority selection and 2 Priority → 1 Regular calling                                                                          |
| Concern about incorrect selections      | Confirmation, with Student ID typed or tapped, or authorized walk-in issuance                                                          |
| Staff need better queue control         | Call Next, Recall, Complete, Hold, and Call Held                                                                                       |
| Need for transaction monitoring         | Queue records and done-only reports                                                                                                    |
| Need to be notified while waiting       | Optional local GSM SMS after **Notify me by SMS** (stored student mobile; walk-in Philippine number and screen token) after print, at 10 or fewer ahead, and when called |


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
8. Store the queue record, including optional `student_id` or `issued_by` / `issue_reason` / `screen_token` for walk-in tickets.
9. Print a 58mm thermal ticket that does not include the student name.
10. Show estimated waiting time only when sufficient completed history is available.
11. Place new tickets in the appropriate service queue.
12. Apply 2 Priority → 1 Regular when calling the next ticket.
13. Allow several windows assigned to the same service to share one waiting queue.
14. Allow staff to view the waiting list, Call Next, Recall, Complete, Hold, and Call Held at their assigned window.
15. Maintain an independent service timer for each window.
16. Display current serving and waiting numbers on the public display, omitting names and held tickets.
17. Optionally send a local GSM SMS when a ticket that opted in is printed, when it first has 10 or fewer ahead in fair call order, and when that number is called or recalled. A ticket already within 10 at print shall not receive the come-back text. Student opt-in uses **Notify me by SMS**, which is off by default and copies the mobile number stored on the student record. Students shall not type a mobile number on Confirm. If no mobile is on file, the control shall stay off and disabled and shall show **No SMS number on file**; Confirm and print shall still proceed. The kiosk shall not display the full mobile number; at most the last four digits may appear after opt-in. Walk-in tickets may use a blank mobile or a Philippine number (`09` and 9 more digits) entered only on the PIN overlay, and that text shall include the screen token. SMS shall not include the student name. Queue operations shall continue if the modem is unavailable.
18. Record queue calls and completion times.
19. Provide staff history of the user’s own completed tickets and administrator history and reports.
20. Allow administrators to manage users and student records, enroll RFID/NFC card serials (UID) and mobile numbers against Student IDs, set the kiosk walk-in PIN, issue walk-in tickets, and generate done-only wait and service reports.
21. Operate on the institution LAN without continuous internet connectivity.
22. Restrict staff and administrator functions to authenticated users. The Guard role cannot log in.
23. Detect when the 58mm thermal printer is out of paper and show an alert on the staff window and System Float so windows can reload the printer. Checked staff or assistant numbers may receive one SMS when the paper first runs out. Stored tickets remain valid. The kiosk print page does not show the alert.



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
9. Every generated ticket is printed on the configured 58mm printer. The name and the walk-in screen token are never printed. If the printer is out of paper, the staff window and System Float show a paper-out alert, and checked staff numbers may receive one SMS; the stored ticket remains valid.
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
20. Optional GSM SMS sends the ticket number after print, at most one come-back text when the ticket first has 10 or fewer ahead, and the window name when the ticket is called or recalled, and only when the ticket opted in. A ticket already within 10 at print skips the come-back text. Position uses fair call order for that service. Held tickets are not counted as waiting.
21. RFID/NFC is an alternate confirm input only. Service and Regular or Priority are selected first. The reader is not used for payment or attendance.
22. Students do not type a mobile number on Confirm. **Notify me by SMS** is off by default. If `students.mobile` is empty, the control is disabled and shows **No SMS number on file**. The full number is not shown; last four digits may appear after opt-in. Walk-in clients may leave the mobile blank or enter `09` and 9 more digits on the PIN overlay. Any other number keeps Issue and Print disabled.
23. Each walk-in ticket stores a screen token: weekday initial, month, day, and three letters, such as T1006ABC for Tuesday 6 October. The token is shown on the cooldown screen and to staff while serving. It is not printed and is not shown on the public display. When a walk-in SMS is sent after print, that text includes the token.
24. The administrator may store staff or assistant name and Philippine mobile under Kiosk settings, with a Send SMS checkbox on each row. Only checked numbers receive one text when the printer first runs out of paper. Unchecked rows are kept and are not texted. Loading paper clears that mark so the next outage can send again.



#### 3.3.4 Use Case Descriptions

The primary actors are the Client, Staff, and Administrator. The system performs validation, queue generation, printing, scheduling, display updating, hold and resume handling, and reporting.

##### 3.3.4.1 Select Service


| Item             | Description                                                                                                |
| ---------------- | ---------------------------------------------------------------------------------------------------------- |
| Actor            | Client                                                                                                     |
| Objective        | Select the required public service                                                                         |
| Precondition     | Kiosk is available                                                                                         |
| Normal Flow      | 1. Client accesses the kiosk. 2. System displays Cashier, Registrar, and DMO. 3. Client selects a service. |
| Alternative Flow | If no valid service is selected, the system does not proceed. Promissory Notes is not shown.               |
| Postcondition    | Selected service is stored for the next step.                                                              |




##### 3.3.4.2 Select Queue Type


| Item             | Description                                                        |
| ---------------- | ------------------------------------------------------------------ |
| Actor            | Client                                                             |
| Objective        | Select Regular or Priority                                         |
| Precondition     | Service has been selected                                          |
| Normal Flow      | 1. System displays Regular and Priority. 2. Client selects a type. |
| Alternative Flow | If no type is selected, the system does not proceed.               |
| Postcondition    | Queue type is stored.                                              |




##### 3.3.4.3 Confirm Queue Request


| Item             | Description                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    |
| ---------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Actor            | Client                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         |
| Objective        | Confirm the request after Student ID validation                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                |
| Precondition     | Service and queue type have been selected                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      |
| Normal Flow      | 1. Client types the Student ID on the keypad or taps an enrolled RFID/NFC card. 2. System looks up the Student ID (or the enrolled card serial). 3. Name may appear for verification. 4. Waiting counts and ETA may appear. 5. After lookup succeeds, **Notify me by SMS** is shown and is off by default. If a mobile number is on file, the client may turn it on; the kiosk does not show the full number (at most last four digits). If no mobile is on file, the control stays off and disabled and shows **No SMS number on file**. Students do not type a phone number. 6. Confirm remains disabled until lookup succeeds. 7. Client confirms. Print proceeds whether or not SMS is on. |
| Alternative Flow | Unknown ID, unknown card, or an already-open ticket today is rejected. The client may type the ID if they did not bring the card, return to a previous step, or use walk-in PIN only when they have no Student ID.                                                                                                                                                                                                                                                                                                                                                                                                                                                                             |
| Postcondition    | A valid student request is submitted for numbering.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            |




##### 3.3.4.4 Issue Walk-in Ticket


| Item             | Description                                                                                                                                                                                                                                                                                                                                                        |
| ---------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| Actor            | Client / authorized issuer                                                                                                                                                                                                                                                                                                                                         |
| Objective        | Issue a ticket without a Student ID                                                                                                                                                                                                                                                                                                                                |
| Precondition     | Kiosk walk-in control is used, or an administrator issues a walk-in ticket                                                                                                                                                                                                                                                                                         |
| Normal Flow      | 1. Client or staff uses the kiosk corner control. 2. System asks for the walk-in PIN every time. 3. Server checks the PIN in settings. 4. Authorized user selects service, priority, and reason. 5. The overlay may leave the mobile blank or accept `09` and 9 more digits. Any other number keeps Issue and Print disabled. If none is entered, no SMS is sent. 6. System issues the ticket and a screen token such as T1006ABC. |
| Alternative Flow | Wrong PIN is rejected. After print or cancel, the next corner click asks for the PIN again.                                                                                                                                                                                                                                                                        |
| Postcondition    | A walk-in ticket is stored with `issued_by`, `issue_reason`, and `screen_token`. The token is shown on the cooldown screen and to staff while serving. Print and the public display remain number-only.                                                                                                                                                                                                                                                              |




##### 3.3.4.5 Generate Queue Number


| Item             | Description                                                                                                                     |
| ---------------- | ------------------------------------------------------------------------------------------------------------------------------- |
| Actor            | System                                                                                                                          |
| Objective        | Generate a unique queue number                                                                                                  |
| Precondition     | Student confirmation or authorized walk-in is valid                                                                             |
| Normal Flow      | 1. System locks the daily counter for the service and date. 2. System increments the counter. 3. System saves the queue record. |
| Alternative Flow | If validation fails, no number is generated.                                                                                    |
| Postcondition    | Queue number and record exist.                                                                                                  |




##### 3.3.4.6 Print Queue Ticket


| Item             | Description                                                                                                                                              |
| ---------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Actor            | System                                                                                                                                                   |
| Objective        | Print the 58mm ticket                                                                                                                                    |
| Precondition     | Queue record has been created                                                                                                                            |
| Normal Flow      | 1. System prepares ticket data. 2. Browser/kiosk sends the print request. 3. XP-58 (XP-Q90EC) prints the number-only ticket.                             |
| Alternative Flow | If the printer is out of paper, the staff window and System Float show a paper-out alert, and checked staff numbers may receive one SMS. The stored ticket remains valid. |
| Postcondition    | Client receives a physical queue reference.                                                                                                              |




##### 3.3.4.7 View Queue Display


| Item          | Description                                                                                                                                |
| ------------- | ------------------------------------------------------------------------------------------------------------------------------------------ |
| Actor         | Client                                                                                                                                     |
| Objective     | Monitor serving and waiting numbers                                                                                                        |
| Precondition  | Public display is operating                                                                                                                |
| Normal Flow   | 1. Display polls current serving and waiting data. 2. Waiting rows use fair call order, maximum 10. 3. Held tickets and names are omitted. |
| Postcondition | Clients see current queue information.                                                                                                     |




##### 3.3.4.8 Call Next Queue


| Item             | Description                                                                                                                                                   |
| ---------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Actor            | Staff                                                                                                                                                         |
| Objective        | Call the next fair-order ticket at this window                                                                                                                |
| Precondition     | Staff is logged in, assigned to a window, and has no open serving ticket                                                                                      |
| Normal Flow      | 1. Staff selects Call Next. 2. System locks the service row. 3. FairQueueScheduler claims the next ticket. 4. Status becomes serving. 5. Window timer starts. |
| Alternative Flow | If no waiting ticket exists, the system reports that none is available.                                                                                       |
| Postcondition    | The ticket is the window’s active transaction.                                                                                                                |




##### 3.3.4.9 Recall Queue


| Item          | Description                                                                                                  |
| ------------- | ------------------------------------------------------------------------------------------------------------ |
| Actor         | Staff                                                                                                        |
| Objective     | Re-announce the current or last called number                                                                |
| Precondition  | A callable ticket exists at the window                                                                       |
| Normal Flow   | 1. Staff selects Recall. 2. System updates the recall token. 3. Display and voice announce the number again. |
| Postcondition | The number is re-announced.                                                                                  |




##### 3.3.4.10 Complete Transaction


| Item             | Description                                                                                                       |
| ---------------- | ----------------------------------------------------------------------------------------------------------------- |
| Actor            | Staff                                                                                                             |
| Objective        | Finish the window’s open ticket                                                                                   |
| Precondition     | The window has an open serving ticket                                                                             |
| Normal Flow      | 1. Staff selects Complete. 2. System records `finished_time`. 3. Queue status becomes `done`. 4. Display updates. |
| Alternative Flow | The system will not complete another window’s open call.                                                          |
| Postcondition    | The ticket is counted as a completed transaction.                                                                 |




##### 3.3.4.11 Hold Queue


| Item          | Description                                                                                                                |
| ------------- | -------------------------------------------------------------------------------------------------------------------------- |
| Actor         | Staff                                                                                                                      |
| Objective     | Set aside the open ticket and free the window                                                                              |
| Precondition  | The window has an open serving ticket                                                                                      |
| Normal Flow   | 1. Staff selects Hold. 2. Open call is finished. 3. Status becomes `held`. 4. Number leaves Now Serving and waiting lists. |
| Postcondition | The window is free. The ticket is not treated as completed.                                                                |




##### 3.3.4.12 Call Held


| Item             | Description                                                                                                                   |
| ---------------- | ----------------------------------------------------------------------------------------------------------------------------- |
| Actor            | Staff                                                                                                                         |
| Objective        | Resume a held ticket at this window                                                                                           |
| Precondition     | No open serving ticket; a held ticket is selected                                                                             |
| Normal Flow      | 1. Staff selects Call on a held row. 2. System assigns the ticket to this window. 3. Status becomes serving. 4. Timer starts. |
| Alternative Flow | Call Held does not use 2 Priority → 1 Regular.                                                                                |
| Postcondition    | The held ticket is again the active transaction.                                                                              |




##### 3.3.4.13 View Queue History


| Item          | Description                                                                                         |
| ------------- | --------------------------------------------------------------------------------------------------- |
| Actor         | Staff / Administrator                                                                               |
| Objective     | Review completed tickets                                                                            |
| Precondition  | User is authenticated                                                                               |
| Normal Flow   | Staff sees own `done` tickets. Administrator sees history, all tickets including held, and reports. |
| Postcondition | Authorized records are displayed. Hold sessions are not counted as served.                          |




##### 3.3.4.14 Manage Users


| Item          | Description                                                                                                         |
| ------------- | ------------------------------------------------------------------------------------------------------------------- |
| Actor         | Administrator                                                                                                       |
| Objective     | Manage admin and staff accounts                                                                                     |
| Precondition  | Administrator is authenticated                                                                                      |
| Normal Flow   | Administrator creates or updates users. Staff window assignment remains unique. Guard cannot be given login access. |
| Postcondition | User records are updated.                                                                                           |




##### 3.3.4.15 Manage Students


| Item          | Description                                                                           |
| ------------- | ------------------------------------------------------------------------------------- |
| Actor         | Administrator                                                                         |
| Objective     | Maintain the Student ID allowlist                                                     |
| Precondition  | Administrator is authenticated                                                        |
| Normal Flow   | Administrator adds or deletes students or imports a CSV. Kiosk lookup uses this list. |
| Postcondition | Authorized Student IDs are current.                                                   |




##### 3.3.4.16 Set Kiosk PIN


| Item          | Description                                                                         |
| ------------- | ----------------------------------------------------------------------------------- |
| Actor         | Administrator                                                                       |
| Objective     | Set the walk-in PIN stored in settings                                              |
| Precondition  | Administrator is authenticated                                                      |
| Normal Flow   | Administrator updates `walkin_pin`. The PIN is not stored in the public kiosk page. |
| Postcondition | Later kiosk walk-in unlocks use the new PIN.                                        |




##### 3.3.4.17 Generate Reports


| Item          | Description                                                                                                     |
| ------------- | --------------------------------------------------------------------------------------------------------------- |
| Actor         | Administrator                                                                                                   |
| Objective     | View average waiting and service times                                                                          |
| Precondition  | Completed tickets exist                                                                                         |
| Normal Flow   | System uses latest completed calls. Wait is created_at to called_time. Service is called_time to finished_time. |
| Postcondition | Overall and per-window averages are shown for `done` tickets only.                                              |




##### 3.3.4.18 Estimate Waiting Time


| Item             | Description                                                                                              |
| ---------------- | -------------------------------------------------------------------------------------------------------- |
| Actor            | System                                                                                                   |
| Objective        | Estimate waiting time for the selected service                                                           |
| Precondition     | Enough completed history and current waiting/serving data exist                                          |
| Normal Flow      | WaitTimeEstimator divides load by active windows. Estimate appears on confirm and may print as one line. |
| Alternative Flow | If fewer than three completed samples exist, the estimate is omitted.                                    |
| Postcondition    | Client sees an estimate only when data are sufficient.                                                   |




##### 3.3.4.19 Manage Multi-Counter Service Queue


| Item          | Description                                                                                                                   |
| ------------- | ----------------------------------------------------------------------------------------------------------------------------- |
| Actor         | Staff / System                                                                                                                |
| Objective     | Let several windows serve one shared queue                                                                                    |
| Precondition  | More than one window is assigned to the same service                                                                          |
| Normal Flow   | Cashier 1–3 share the Cashier waiting queue. Each keeps its own open call and timer. FairScheduler prevents duplicate claims. |
| Postcondition | Several counters serve one queue without assigning the same ticket twice.                                                     |




### 3.4 System Analysis and Design



#### 3.4.1 Business Process Analysis

Existing process. Students and other clients go directly to the Cashier, Registrar, or DMO and wait in a first-come-first-served line. Staff watch the line and call the next person by physical order. During busy periods this produces unclear order, line cutting, difficulty serving priority clients, overcrowding, and no shared view of who is waiting.

Proposed process. The client starts at the kiosk, selects service and Regular or Priority, and confirms with a typed or tapped Student ID or an authorized walk-in PIN path. The system stores the ticket and prints a 58mm number-only ticket. Optional GSM SMS is sent only after **Notify me by SMS** is turned on (stored student mobile) or after a valid walk-in number is entered on the PIN overlay: after print, when the ticket first has 10 or fewer ahead, and when it is called. Staff call by 2 Priority → 1 Regular across shared windows, and may Hold or Call Held. The public display shows serving and waiting numbers only. The administrator monitors completed-transaction reports.

#### 3.4.2 System Architecture

The system is standalone and LAN-based [11]. One mini PC (Intel Core i5 8th Generation, 8 GB RAM) serves as both the public kiosk host and the application server. Staff PCs, the public display, and the administrator computer connect to that mini PC over the LAN.

Mini PC (kiosk and server). Runs Windows, XAMPP/Laravel, and the kiosk browser. The touchscreen is the public client interface. USB ports hold the 58mm printer, ACR122U-A9 reader, and SIM7600G-H GSM module.

Touchscreen monitor. Public three-step student flow, RFID/NFC tap or keypad on confirm, and corner PIN walk-in issuance.

58mm USB thermal printer. XP-58 (XP-Q90EC). Prints the number and optional ETA line. No name. Paper-out is reported on the staff window and System Float, with an optional SMS to checked staff numbers.

RFID/NFC reader. ACR122U-A9 USB reader at the kiosk; enrolled ISO 14443A / MIFARE card serial maps to Student ID.

GSM/LTE USB module. SIM7600G-H on the mini PC; local SMS, not a cloud SMS API.

LAN. Connects the mini PC, staff PCs, public display, and administrator PC.

Application server. Laravel 12 on the same mini PC, with Blade, Vite/Tailwind, and services for fair scheduling, wait estimation, student-ticket guarding, walk-in PIN checks, paper-out alerts, and optional SMS dispatch.

MySQL/MariaDB. Stores services, windows, users, students, settings, paper_sms_contacts, daily_queue_counters, queues, and queue_calls.

Staff windows. Call Next, Recall, Complete, Hold, Call Held, and per-window timers.

Public display. Serving and waiting numbers in fair order; no names; no held tickets.

Administrator dashboard. Users, students, kiosk PIN, paper-out SMS list, walk-in issue, history, and done-only reports.

#### 3.4.3 Unified Modeling Language (UML)



##### 3.4.3.1 Use Case Diagram

Student/Client: select service, select Regular or Priority, type or tap Student ID, optionally turn on **Notify me by SMS** (off by default; stored student mobile only), view ETA when available, confirm, receive printed number.

Walk-in path: enter kiosk PIN, issue walk-in ticket, see the screen token on the cooldown screen, receive printed number.

Staff: log in, view waiting list, Call Next, Recall, Complete, Hold, Call Held, view name or walk-in reason while serving, receive a paper-out alert on the staff window and System Float.

Administrator: log in, manage users, manage students (RFID/NFC card UID and mobile), set kiosk PIN, issue walk-in ticket, view reports.

Public display: show Now Serving and waiting numbers only.

Database: store students, settings, paper SMS contacts, queues (`student_id` or `issued_by` / `issue_reason` / `screen_token`), counters, and queue calls.

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

Services relate to windows, queues, and daily_queue_counters. Students supply the allowlist used when `queues.student_id` is present. Walk-in tickets leave `student_id` null and store `issued_by` and `issue_reason`. Users have role `admin`, `staff`, or `guard`. Staff users have a unique `window_id`. The guard user cannot log in. Settings store `walkin_pin`. `paper_sms_contacts` stores staff mobiles for the paper-out text. Walk-in tickets may store `screen_token`. Queue_calls link a ticket to a window with `called_time` and `finished_time`. Any ERD drawing placed in the manuscript is a placeholder and must be replaced so it shows these entities.

#### 3.4.6 Database Design

The database is MySQL/MariaDB. Domain tables are services, windows, users, students, settings, daily_queue_counters, queues, and queue_calls. Laravel `migrations` may exist in the environment but is not a queue-domain table.

##### 3.4.6.1 Database Schema

`services` stores service name and ticket prefix. `windows` stores window name, group, assigned service, and status. `users` stores name, login, password, role, and optional unique window. `students` stores authorized Student ID, name, optional card serial, and optional mobile. `settings` stores `walkin_pin`. `paper_sms_contacts` stores staff mobiles for the paper-out text. `daily_queue_counters` stores last number per service and date. `queues` stores number, service, optional student, optional issuer and reason, optional screen token, optional SMS number and notice flags, priority, status, date, and timestamps. `queue_calls` stores queue, window, called time, and finished time. Any schema drawing placed in the manuscript is a placeholder and must be replaced so it matches this structure.

##### 3.4.6.2 Data Dictionary

**Table 3.4 Data Dictionary – Services**


| Field Name   | Data Type   | Key | Description                |
| ------------ | ----------- | --- | -------------------------- |
| id           | BIGINT      | PK  | Service identifier         |
| service_name | VARCHAR(50) | —   | Service name               |
| prefix       | VARCHAR(5)  | —   | Ticket prefix (C, R, D, P) |
| description  | TEXT        | —   | Service description        |
| created_at   | TIMESTAMP   | —   | Record created             |
| updated_at   | TIMESTAMP   | —   | Record updated             |


**Table 3.5 Data Dictionary – Windows**


| Field Name  | Data Type   | Key | Description                        |
| ----------- | ----------- | --- | ---------------------------------- |
| id          | BIGINT      | PK  | Window identifier                  |
| window_name | VARCHAR(50) | —   | Window name (Cashier 1, Registrar) |
| group_name  | VARCHAR(50) | —   | Display group                      |
| service_id  | BIGINT      | FK  | Assigned service                   |
| status      | ENUM        | —   | active or inactive                 |
| created_at  | TIMESTAMP   | —   | Record created                     |
| updated_at  | TIMESTAMP   | —   | Record updated                     |


**Table 3.6 Data Dictionary – Users**


| Field Name | Data Type    | Key        | Description                                     |
| ---------- | ------------ | ---------- | ----------------------------------------------- |
| id         | BIGINT       | PK         | User identifier                                 |
| name       | VARCHAR(100) | —          | User name                                       |
| email      | VARCHAR(100) | —          | Login email                                     |
| password   | VARCHAR(255) | —          | Password (bcrypt or accepted legacy dump value) |
| role       | ENUM         | —          | admin, staff, or guard                          |
| window_id  | BIGINT       | FK, unique | Staff window; null for admin and guard          |
| created_at | TIMESTAMP    | —          | Record created                                  |
| updated_at | TIMESTAMP    | —          | Record updated                                  |


Guard exists as the kiosk issuer row and cannot log in.

**Table 3.7 Data Dictionary – Students**


| Field Name | Data Type    | Key              | Description                                             |
| ---------- | ------------ | ---------------- | ------------------------------------------------------- |
| id         | BIGINT       | PK               | Record identifier                                       |
| student_id | VARCHAR(50)  | Unique           | Authorized Student ID                                   |
| name       | VARCHAR(100) | —                | Student name for confirm and staff serving              |
| card_uid   | VARCHAR(32)  | Unique, nullable | Enrolled RFID/NFC serial for tap confirm                |
| mobile     | VARCHAR(20)  | —                | Stored number for GSM SMS; not typed at student Confirm |
| created_at | TIMESTAMP    | —                | Record created                                          |
| updated_at | TIMESTAMP    | —                | Record updated                                          |


**Table 3.8 Data Dictionary – Settings**


| Field Name    | Data Type    | Key    | Description                 |
| ------------- | ------------ | ------ | --------------------------- |
| id            | BIGINT       | PK     | Setting identifier          |
| setting_key   | VARCHAR(64)  | Unique | Setting name (`walkin_pin`) |
| setting_value | VARCHAR(255) | —      | Stored value                |
| updated_at    | TIMESTAMP    | —      | Last update                 |


**Table 3.9 Data Dictionary – Daily Queue Counters**


| Field Name  | Data Type | Key | Description        |
| ----------- | --------- | --- | ------------------ |
| id          | BIGINT    | PK  | Counter identifier |
| service_id  | BIGINT    | FK  | Service            |
| queue_date  | DATE      | —   | Operating date     |
| last_number | INT       | —   | Last issued number |
| created_at  | TIMESTAMP | —   | Record created     |
| updated_at  | TIMESTAMP | —   | Record updated     |


**Table 3.10 Data Dictionary – Queues**


| Field Name    | Data Type   | Key          | Description                                                                                        |
| ------------- | ----------- | ------------ | -------------------------------------------------------------------------------------------------- |
| id            | BIGINT      | PK           | Ticket identifier                                                                                  |
| queue_number  | VARCHAR(20) | —            | Printed number                                                                                     |
| service_id    | BIGINT      | FK           | Selected service                                                                                   |
| student_id    | VARCHAR(50) | FK, nullable | Student ticket only                                                                                |
| issued_by     | BIGINT      | FK, nullable | Walk-in issuer user id                                                                             |
| issue_reason  | VARCHAR(32) | —            | Walk-in reason                                                                                     |
| priority      | TINYINT     | —            | 0 Regular, 1 Priority                                                                              |
| status        | ENUM        | —            | waiting, serving, done, cancelled, held                                                            |
| queue_date    | DATE        | —            | Issue date                                                                                         |
| created_at    | TIMESTAMP   | —            | Used for wait averages                                                                             |
| screen_token  | VARCHAR(12) | —            | Walk-in cooldown token, such as T1006ABC; not printed                                              |
| sms_mobile    | VARCHAR(20) | —            | Ticket SMS number: copied from `students.mobile` when SMS is on, or entered on the walk-in overlay |
| sms_issued    | TINYINT     | —            | 1 after the ticket-number text is queued                                                           |
| sms_near      | TINYINT     | —            | 1 after the 10-away text is queued, or when the ticket was already within 10 at issue              |
| updated_at    | TIMESTAMP   | —            | Last update                                                                                        |


Name is never stored on the ticket row. Walk-in tickets leave `student_id` null.

**Table 3.11 Data Dictionary – Queue Calls**


| Field Name    | Data Type | Key | Description                                       |
| ------------- | --------- | --- | ------------------------------------------------- |
| id            | BIGINT    | PK  | Call identifier                                   |
| queue_id      | BIGINT    | FK  | Ticket                                            |
| window_id     | BIGINT    | FK  | Serving window                                    |
| called_time   | DATETIME  | —   | Time called; open call when finished_time is null |
| finished_time | DATETIME  | —   | Time completed or hold session closed             |
| created_at    | TIMESTAMP | —   | Record created                                    |
| updated_at    | TIMESTAMP | —   | Record updated                                    |


Reports use the latest completed call of a `done` ticket. Hold sessions are excluded from served counts.

**Table 3.12 Data Dictionary Summary**


| Table name           | Main purpose                                                                                   |
| -------------------- | ---------------------------------------------------------------------------------------------- |
| services             | Stores available services and queue prefixes                                                   |
| windows              | Stores service counters and their assigned services                                            |
| users                | Stores authorized administrator and staff accounts                                             |
| students             | Stores authorized Student IDs, names, optional card serials (UID), and optional mobile numbers |
| settings             | Stores the kiosk walk-in PIN (`walkin_pin`); the PIN is not published here                     |
| paper_sms_contacts   | Staff or assistant mobiles and the Send SMS checkbox for the printer paper-out text            |
| daily_queue_counters | Maintains the last queue number per service and date                                           |
| queues               | Stores generated queue tickets and their statuses                                              |
| queue_calls          | Records calls, windows, called time, and finished time                                         |


**Table 3.13 Data Dictionary – Paper SMS Contacts**


| Field Name | Data Type   | Key    | Description                                                             |
| ---------- | ----------- | ------ | ----------------------------------------------------------------------- |
| id         | BIGINT      | PK     | Contact identifier                                                      |
| name       | VARCHAR(80) | —      | Staff or assistant label                                                |
| mobile     | VARCHAR(20) | Unique | Philippine mobile stored as +639 and 9 digits                           |
| enabled    | TINYINT     | —      | 1 means Send SMS is checked; unchecked rows are kept and are not texted |


Checked contacts receive one text when the printer first runs out of paper. Loading paper clears that mark.

#### 3.4.7 System Flowchart

The client uses the kiosk. A student completes three steps and Student ID validation by keypad or RFID/NFC tap. After lookup, **Notify me by SMS** is off by default and uses the stored student mobile; students do not type a number. A walk-in user unlocks with the PIN, enters a reason, may enter a Philippine mobile or leave it blank, and receives a screen token such as T1006ABC. The system increments the daily counter, stores the ticket, prints the number-only 58mm ticket, and updates the display. Optional GSM SMS is sent when an opted-in ticket is printed, when it first has 10 or fewer ahead, and when it is called. Staff call by 2 Priority → 1 Regular, or resume a held ticket. Status moves among waiting, serving, held, and done. The administrator reviews users, students, card UID and mobile enrollment, the walk-in PIN, the paper-out SMS list, and done-only reports. The system stays on the LAN. Any flowchart drawing placed in the manuscript is a placeholder and must be replaced so it shows the typed-ID path, the RFID/NFC tap path, the walk-in PIN path, GSM SMS notices, plus Hold and Call Held.

#### 3.4.8 User Interface Design

The implemented interfaces are described from the current system. Screenshots or wireframes placed beside this section are placeholders and must be replaced with captures of the live kiosk, staff panel, administrator dashboard, and public display. The text below is the description of record.

Kiosk. The public kiosk is a three-step flow on a compact 1024×600 layout: Cashier, Registrar, and DMO; Regular or Priority; then confirmation with a summary column, Student ID keypad, and RFID/NFC reader. Confirm stays disabled until lookup succeeds. The matched name may appear for checking only. Waiting counts and estimated wait may appear when data are sufficient. **Notify me by SMS** is off by default and uses the stored student mobile; students do not type a number. If no mobile is on file, the control is disabled and shows **No SMS number on file**. The full number is not shown. Promissory Notes is hidden. A small bottom-right control opens the walk-in PIN pad on every use. After a correct PIN, the overlay issues a walk-in ticket with service, priority, reason, and a blank mobile or a Philippine mobile (`09` and 9 more digits). Any other number keeps Issue and Print disabled. After print, the cooldown screen shows the number and the screen token, then a countdown returns to the first step. The student name and the walk-in token are never printed.

Staff panel. Authorized staff see the current number, student name or Walk-in (reason) and screen token, an independent window timer, a fair waiting list of up to 10, and a held list. Controls are Call Next, Recall, Complete, Hold, and Call Held. Shortcuts are Alt+N, Alt+R, and Alt+C. An optional System Float window can stay on top on the staff PC and shows the same paper-out alert. Staff history shows that user’s own done tickets. When the thermal printer has no paper or a ticket stays stuck in Printing, a paper-out alert appears on the staff window and System Float so the roll can be replaced. Checked staff numbers may receive one SMS.

Administrator dashboard. The PECIT panel shows live totals and waiting information, users, students (add, delete, CSV import, RFID/NFC card UID and mobile), kiosk settings (walk-in PIN and printer paper SMS), walk-in issue, history, all tickets including held, and done-only wait and service reports. There is no public Services CRUD screen.

Public display. Windows are grouped by `group_name`: Window 1 Cashier 1–3, Window 2 Promissory Notes, Window 3 DMO, Window 4 Registrar. The screen shows Now Serving and up to 10 waiting numbers in the same fair order as Call Next. Names and held tickets are omitted. Voice announcement may be enabled.

### 3.5 Operational Environment



#### 3.5.1 Hardware Requirements

**Table 3.14 Hardware Requirements**


| Hardware Component         | Configuration                                                                |
| -------------------------- | ---------------------------------------------------------------------------- |
| Mini PC (kiosk and server) | Intel Core i5 8th Generation, 8 GB RAM; hosts the kiosk UI and Laravel/XAMPP |
| Touchscreen monitor        | Public three-step kiosk, attached to the mini PC                             |
| 58mm USB thermal printer   | XP-58 (XP-Q90EC); paper-out alert on the staff window and System Float                     |
| RFID/NFC reader            | ACR122U-A9 USB, ISO 14443A / MIFARE, at kiosk confirm                        |
| GSM/LTE USB module         | SIM7600G-H on the mini PC; SIM and load required                             |
| HDMI monitor               | Public queue display                                                         |
| AVR HDMI box               | Display connection                                                           |
| Staff workstation          | One per window                                                               |
| Administrator computer     | Users, students, PIN, reports                                                |
| Keyboard and mouse         | Standard USB                                                                 |
| LAN router or switch       | Local communication                                                          |
| Cables and adapters        | USB, HDMI, AVR, Ethernet, power                                              |




#### 3.5.2 Software Requirements

**Table 3.15 Software Requirements**


| Software                  | Purpose                      |
| ------------------------- | ---------------------------- |
| Windows 10/11             | Host and workstation OS      |
| XAMPP                     | Local Apache/PHP/MySQL       |
| PHP 8.2+                  | Server runtime               |
| Laravel 12                | Application framework        |
| Blade, Vite, Tailwind CSS | Interfaces and assets        |
| MySQL/MariaDB             | Local database               |
| Visual Studio Code        | Development editor           |
| Composer, Node.js/npm     | Dependencies                 |
| Chrome or Edge            | Kiosk, staff, display, admin |
| Git                       | Version control              |




#### 3.5.3 Network Requirements

The kiosk, application host, staff PCs, display, and administrator computer communicate on the campus LAN. The application host is the same mini PC that drives the kiosk. Wired Ethernet is preferred. Continuous internet access is not required for ticket issuance, calling, display, or reports [11]. GSM SMS uses the SIM7600G-H module and a loaded SIM.

#### 3.5.4 User Environment

Students and parents use the kiosk and public display. Walk-in clients use the PIN path. Staff use the window panel and optional System Float. The administrator uses the panel for users, students, PIN, walk-in issue, and reports. The Guard role has no login screen.

### 3.6 System-to-System Interface

The kiosk posts confirmed student or walk-in requests to the Laravel application. The application writes queues and counters, then the kiosk prints. Staff posts change queue_calls and queue status. The display and staff panel read serving, waiting, and held data. The administrator reads and writes users, students, and settings.

### 3.7 Communication Interface



#### 3.7.1 Internal Communication

**Table 3.16 Internal Communication Interface**


| Source      | Destination     | Data                                                           | Purpose                                |
| ----------- | --------------- | -------------------------------------------------------------- | -------------------------------------- |
| Kiosk       | Application     | Service, priority, Student ID or walk-in PIN/reason            | Validate and create ticket             |
| Application | Database        | Queue, student or issuer, priority, status, counters, settings | Store and retrieve                     |
| Application | Thermal printer | Number and optional ETA                                        | Print number-only ticket               |
| Mini PC     | Staff panel     | Printer paper status                                           | Paper-out alert on the staff window and System Float; one SMS to checked staff numbers |
| Database    | Staff panel     | Waiting, serving, held, name or walk-in reason                 | Window operations                      |
| Database    | Admin dashboard | Users, students, PIN, history, reports                         | Administration                         |
| Database    | Public display  | Serving and waiting numbers                                    | Public monitoring                      |




#### 3.7.2 External Communication

**Table 3.17 External Communication Interface**


| External Entity      | Interface         | Information sent                             | System response                                                           |
| -------------------- | ----------------- | -------------------------------------------- | ------------------------------------------------------------------------- |
| Students and parents | Kiosk             | Service, priority, Student ID, confirm       | Validated ticket and print                                                |
| Walk-in clients      | Kiosk PIN overlay | PIN, service, priority, reason               | Walk-in ticket and print                                                  |
| Students and parents | Public display    | —                                            | Serving and waiting numbers                                               |
| Administrative staff | Staff panel       | Call Next, Recall, Complete, Hold, Call Held | Updated window and display; paper-out alert when the printer has no paper |
| System administrator | Admin panel       | Users, students, PIN, reports                | Authorized administration                                                 |




### 3.8 Development Methodology

The system was developed using the Waterfall model because the offices, kiosk hardware, printer, LAN, and queue rules were known before coding [19]. The five phases were Requirements Analysis, System Design, Implementation/Development, Testing, and Deployment.

*Figure 3.11 Waterfall Development Model Used in the Study*

#### 3.8.1 Software Development Model

Requirements analysis used the questionnaire, interviews, observation, and document review. Design produced the architecture, use cases, DFD, ERD, schema, flowchart, and interfaces. Implementation used Laravel 12, PHP, Blade, Vite/Tailwind, and MySQL/MariaDB, including FairQueueScheduler, WaitTimeEstimator, StudentQueueGuard, and KioskWalkInGate. Testing covered the functions listed in Section 3.11. Deployment configured the mini PC as both kiosk and server, the XP-Q90EC printer, ACR122U-A9 reader, SIM7600G-H module, staff PCs, display, and LAN.

#### 3.8.2 Development Approach

Each phase was completed before the next. The kiosk chrome was kept as a locked three-step flow. Staff and administrator screens used the PECIT panel theme. Queue rules were implemented in server-side services rather than in the browser alone.

#### 3.8.3 Development Tools

**Table 3.18 Development Tools**


| Tool                      | Purpose                                |
| ------------------------- | -------------------------------------- |
| Laravel 12                | Backend and routing                    |
| PHP 8.2+                  | Server language                        |
| MySQL/MariaDB             | Data store                             |
| XAMPP                     | Local server                           |
| Visual Studio Code        | Editing                                |
| Blade, Vite, Tailwind CSS | Interfaces                             |
| JavaScript                | Kiosk keypad, display poll, staff poll |
| Chrome / Edge             | Runtime and kiosk printing             |
| Git                       | Source history                         |




#### 3.8.4 Version Control

Git recorded source changes so modules could be revised without losing earlier working versions.

#### 3.8.5 Deployment Strategy

The application is installed on the local server or kiosk host. Staff and display PCs open the LAN URL. The thermal printer and RFID/NFC reader are attached to the kiosk PC. The GSM/LTE module is attached to the application host. Chrome kiosk flags support silent printing. Staff PCs may use the System Float helper. The database and session files remain local.

### 3.9 System Workflow and Operations



#### 3.9.1 System Workflow

The student selects service and priority, types or taps a Student ID, may turn on **Notify me by SMS** (off by default; stored mobile), and confirms. The application validates the ID and the one-open-ticket rule, then prints. A walk-in user unlocks with the PIN, enters a reason, may leave the mobile blank or enter a Philippine number, sees a screen token on the cooldown screen, and receives a number-only ticket. Staff call from the shared fair list or resume a held ticket. Optional GSM SMS is sent after print, at 10 or fewer ahead, and when called. Completed tickets supply report averages.

#### 3.9.2 Operational Workflow

1. Client opens the kiosk.
2. Client selects Cashier, Registrar, or DMO.
3. Client selects Regular or Priority.
4. Student path: type or tap Student ID, wait for lookup, optionally turn on **Notify me by SMS** if a number is on file, confirm.
5. Walk-in path: corner control, PIN, reason, blank or Philippine mobile, confirm. The cooldown screen shows the screen token.
6. System generates and stores the number.
7. Printer issues the 58mm ticket.
8. Display and staff lists update. Optional GSM SMS is queued when applicable.
9. Staff Call Next, Recall, Hold, Call Held, or Complete.
10. Completed tickets appear in history and reports.



#### 3.9.3 Module Interaction

**Table 3.19 Module Interaction**


| Module          | Primary function                                              | Interacts with                        |
| --------------- | ------------------------------------------------------------- | ------------------------------------- |
| Kiosk           | Three-step student flow and PIN walk-in                       | Application, database, printer        |
| Application     | Validation, numbering, scheduling, ETA, PIN check             | All modules                           |
| Database        | Students, settings, paper SMS contacts, queues, calls         | Application                           |
| Thermal printer | Number-only ticket                                            | Kiosk / application                   |
| Staff panel     | Call Next, Recall, Complete, Hold, Call Held, paper-out alert | Application, database, printer status |
| Public display  | Fair serving and waiting numbers                              | Application, database                 |
| Administrator   | Users, students, PIN, paper SMS list, walk-in issue, reports  | Application, database                 |




#### 3.9.4 Input–Process–Output Workflow

**Table 3.20 Input–Process–Output Workflow**


| Input                  | Process                           | Output                       |
| ---------------------- | --------------------------------- | ---------------------------- |
| Service and priority   | Validate selection                | Ready confirmation           |
| Student ID             | Lookup and one-open-ticket check  | Validated student request    |
| Walk-in PIN and reason | Server PIN check                  | Authorized walk-in request and screen token |
| Valid request          | Increment counter and store queue | Queue record                 |
| Queue record           | Print 58mm ticket                 | Physical number reference    |
| Queue and call updates | Refresh display and staff lists   | Serving, waiting, held views |
| Complete               | Mark done and close call          | History and report sample    |




### 3.10 Project Management Plan

The plan followed Waterfall from requirements through deployment and documentation. A Gantt chart recorded the sequence of chapter writing, design, development, testing, evaluation, and submission.

#### 3.10.1 Gantt Chart

**Table 3.21 Gantt Chart for January to May**


| Activity                       | January   | February  | March     | April     | May       |
| ------------------------------ | --------- | --------- | --------- | --------- | --------- |
| Topic identification and title | Weeks 1–4 |           |           |           |           |
| Chapter 1                      |           | Weeks 1–4 |           |           |           |
| Chapter 2                      |           |           | Weeks 1–4 |           |           |
| Chapter 3                      |           |           |           | Weeks 1–4 | Weeks 1–2 |


**Table 3.22 Gantt Chart for June to October**


| Activity                           | June      | July      | August    | September |
| ---------------------------------- | --------- | --------- | --------- | --------- |
| System analysis and design         | Weeks 1–4 |           |           |           |
| System development                 |           | Weeks 1–4 |           |           |
| Testing and debugging              |           |           | Weeks 1–3 |           |
| Deployment and evaluation          |           |           | Week 4    | Weeks 1–2 |
| Chapters 4 and 5 and final editing |           |           |           | Weeks 2–4 |




#### 3.10.2 Budget and Resource Allocation

Hardware used for development and testing was already available and is not treated as a purchased project cost.

**Table 3.23 Hardware Resources**


| Item                       | Quantity  | Remarks                                |
| -------------------------- | --------- | -------------------------------------- |
| Mini PC (kiosk and server) | 1         | Intel Core i5 8th Generation, 8 GB RAM |
| Touchscreen monitor        | 1         | Public kiosk display                   |
| 58mm USB thermal printer   | 1         | XP-Q90EC ticket printing               |
| USB RFID/NFC reader        | 1         | ACR122U-A9 at kiosk confirm            |
| GSM/LTE USB module         | 1         | SIM7600G-H on the mini PC              |
| HDMI monitor               | 1         | Public display                         |
| Staff workstation          | 1 or more | Window operations                      |
| Administrator computer     | 1         | Administration                         |
| LAN router or switch       | 1         | Local network                          |
| Cables and adapters        | As needed | Device connections                     |




#### 3.10.3 Risk Management Plan

**Table 3.24 Risk Management Plan**


| Potential risk        | Possible impact               | Mitigation                                                              |
| --------------------- | ----------------------------- | ----------------------------------------------------------------------- |
| Printer out of paper  | No ticket print               | Staff window and System Float paper-out alert; one SMS to checked staff numbers; spare roll; reprint from stored ticket |
| Power interruption    | Temporary stop                | Local file/database recovery after restart                              |
| LAN failure           | Display and staff out of sync | Inspect switch and cables before opening                                |
| Software defect       | Wrong ticket or call          | Unit, integration, and UAT before deployment                            |
| Student list error    | Eligible ID rejected          | Admin student maintenance and clear kiosk errors                        |
| Wrong walk-in PIN use | Unauthorized walk-in          | Server-side PIN, ask PIN on every corner click                          |
| Schedule delay        | Late documentation            | Follow the Gantt and keep testing time                                  |




### 3.11 Verification, Validation, and Testing Plans

Testing followed implementation and supported Specific Objective 5: evaluate functionality, usability, efficiency, reliability and accuracy, and user satisfaction through system testing, expert evaluation, and UAT using ISO/IEC 25010-aligned characteristics [10].

#### 3.11.1 Test Planning

**Table 3.25 Test Planning**


| Testing activity          | Objective                                       | Expected outcome                    |
| ------------------------- | ----------------------------------------------- | ----------------------------------- |
| Test-case preparation     | Cover kiosk, walk-in PIN, staff, display, admin | Complete cases                      |
| Environment setup         | Kiosk, printer, LAN, staff, display             | Ready test bed                      |
| Functional testing        | Verify rules and modules                        | Requirements covered                |
| Integration testing       | Verify component exchange                       | Consistent ticket and display state |
| UAT and expert evaluation | Measure acceptance and quality                  | Documented ratings                  |




#### 3.11.2 Unit Testing

Unit testing checked service and priority selection, Student ID lookup, one-open-ticket rejection, walk-in PIN accept/reject, counter increment, print payload, paper-out alert, fair claim, Hold, Call Held, complete, and report filters.

#### 3.11.3 Integration Testing

Integration testing verified that a confirmed student or walk-in ticket appears in the staff list and display, that Call Next on two cashier windows cannot claim the same ticket, that Hold removes the number from Now Serving, and that administrator reports exclude hold sessions.

#### 3.11.4 System Testing

System testing covered the full path on the LAN at the selected PECIT offices: kiosk, printer, staff, display, and administrator. After the system was implemented, User Acceptance Testing used the Cashier, Registrar, DMO, and Student System Feedback Forms. One IT expert who tested the developed system completed the Technical Expert System Evaluation Form. Expert ratings are not combined with the UAT means. The Chapter 3 student-and-parent questionnaire is the needs-assessment survey and is not reused as the Chapter 4 evaluation.

### 3.12 Ethical Considerations

Permission was obtained from PECIT before data gathering and testing. Participation was voluntary. Only information needed for queue validation and management was collected, consistent with the Data Privacy Act of 2012 and its implementing rules [18], [14]. Student ID is used for confirmation and staff serving. Names are not printed or shown on the public display. Walk-in reasons are visible to staff only. The walk-in PIN is stored in settings and checked on the server. Database access is limited to authorized accounts. Findings are reported without fabrication.

These procedures protect participants while allowing the system to replace the manual first-come-first-served line with an organized, monitored, and recorded queue process.

# CHAPTER 4



## SYSTEM IMPLEMENTATION, RESULTS, AND DISCUSSION

This chapter presents the implementation of the Standalone Kiosk-Based Queuing Management System with Thermal Printer, the modules and interfaces placed in operation at PECIT, the testing and evaluation results, and the discussion of those results against the five specific objectives. The implemented system replaces the manual first-come-first-served line at the Cashier, Registrar, and Data Management Office (DMO) with a standalone LAN-based kiosk process.

### 4.1 System Overview

The system was developed and implemented for the selected student service offices of the Philippine Electronics and Communication Institute of Technology (PECIT), specifically the Cashier, Registrar, and DMO. It organizes queue generation, ticket issuance, fair multi-window calling, public monitoring, and administrative recording.

The system operates on the institution LAN. One mini PC (Intel Core i5 8th Generation, 8 GB RAM) serves as both the public kiosk host and the application server. Attached to that mini PC are a touchscreen monitor, a 58mm USB thermal printer (XP-58 / XP-Q90EC), a USB RFID/NFC reader (ACR122U-A9), and a GSM/LTE USB module (SIM7600G-H). Staff workstations, an administrator computer, a public display, and a local MySQL/MariaDB database connect over the LAN. Essential queue operations do not require continuous internet connectivity [11]. GSM SMS is optional and uses the local SIM7600G-H module, not a public cloud SMS API. When the thermal printer is out of paper, the staff window and System Float show a paper-out alert, and checked staff numbers may receive one SMS.

A student completes three kiosk steps: service, Regular or Priority, and confirmation. During confirmation the Student ID is typed or read from an enrolled RFID/NFC card and validated against the authorized student list, and a second open ticket for the same ID on the same day is rejected. Clients who do not bring the card may still type the Student ID. Clients who cannot use a Student ID may request a walk-in ticket at the same kiosk after the walk-in PIN stored in settings is accepted. **Notify me by SMS** is off by default. Students do not type a mobile number; notices use the number stored on the student record. If no mobile is on file, the control stays off and disabled and shows **No SMS number on file**; print still proceeds. Walk-in clients may leave the mobile blank or enter a Philippine number on the PIN overlay, and the cooldown screen shows a token such as T1006ABC. The printed ticket and public display remain number-only. SMS stays name-free, and a walk-in text includes that token. The student name may appear on confirmation and to staff while serving.

Staff call tickets by 2 Priority → 1 Regular across windows that share a service. Cashier 1, Cashier 2, and Cashier 3 share one Cashier waiting queue while keeping independent open calls and timers. Staff may Recall, Complete, Hold, or Call Held. Administrators manage users and student records, set the kiosk walk-in PIN, may issue walk-in tickets, and generate reports from completed transactions only. The Guard role is the kiosk issuer record and cannot log in.

### 4.2 System Features

The implemented system provides the following major features:

Service selection. The public kiosk presents Cashier, Registrar, and DMO. Promissory Notes is hidden from the public list.

Priority selection. The client chooses Regular or Priority. Call Next later applies 2 Priority → 1 Regular.

Transaction confirmation. The third kiosk step requires a valid Student ID lookup before Confirm is enabled, by keypad or RFID/NFC tap, or an authorized walk-in path after the PIN is accepted.

Automatic queue-number generation. After a valid confirmation or walk-in issue, a locked daily counter increments the number for that service and date.

Thermal ticket printing. The 58mm printer issues a number-only ticket. Estimated waiting time may appear as one compact line when sufficient completed history exists. The student name and the walk-in screen token are never printed.

Real-time queue display. The public monitor shows Now Serving and up to 10 waiting numbers in the same fair order used by Call Next. Names and held tickets are omitted.

Staff queue management. Authenticated staff can Call Next, Recall, Complete, Hold, and Call Held at their assigned window. Each window has an independent service timer.

Transaction recording. Queue records and queue calls support monitoring and done-only wait and service reports.

Student ID validation and privacy. Student ID is typed or tapped only on confirmation. Names are not printed, shown on the public display, or sent by SMS. The kiosk does not show the full student mobile number.

Authorized walk-in issuance. The kiosk corner control asks for the walk-in PIN on every use. The server checks the PIN. Several walk-in tickets may be open at once. Each walk-in stores a screen token such as T1006ABC. Staff see Walk-in (reason) and that token. The token appears on the cooldown screen and in the issued SMS when a mobile was entered. Print and the public display stay number-only. The mobile may be blank or `09` and 9 more digits. Forgetting the physical ID is not a walk-in.

RFID/NFC confirmation. An enrolled ISO 14443A / MIFARE card serial is read by the ACR122U-A9 and mapped to a Student ID. Unknown cards are rejected. Service and Regular or Priority are still selected first.

GSM SMS notices. After a successful Student ID lookup, **Notify me by SMS** is off by default. Turning it on copies the stored student mobile onto the ticket. Students do not type a number. If no mobile is on file, the control is disabled and shows **No SMS number on file**. Walk-in tickets may use a blank mobile or a Philippine number entered only on the PIN overlay. An opted-in ticket may receive a local GSM text through the SIM7600G-H module after print, a come-back text the first time it has 10 or fewer ahead in fair call order, and the window name when the number is called or recalled. A ticket already within 10 at print does not get the come-back text. A walk-in text includes the screen token. The sender on the kiosk PC finds the modem command port and includes the Smart message center in each text. Call Next does not wait on the modem. The kiosk does not show the full mobile number.

Paper-out alert. When the XP-Q90EC printer has no paper, or a ticket stays stuck in Printing, the staff window and System Float show an alert. Checked staff or assistant numbers receive one SMS: the printer is out of paper. Loading a new roll clears that mark. The stored ticket remains valid. The kiosk print page does not show the alert.

### 4.3 System Modules

The developed system consists of four major modules.

#### 4.3.1 Student Kiosk Module

The Student Kiosk Module is the public entry point. A student selects a service and Regular or Priority, then types a Student ID on the confirmation keypad or taps an enrolled RFID/NFC card. The system looks up the ID, may show the name for verification, may show waiting counts and estimated wait, and keeps Confirm disabled until lookup succeeds. **Notify me by SMS** is off by default and uses the stored student mobile; students do not type a number. After confirmation it stores the ticket and prints the 58mm number. A walk-in user uses the corner PIN control, selects service, priority, and reason, may leave the mobile blank or enter a Philippine number on the overlay, and receives the same number-only print. After print, the cooldown screen shows the screen token, then a countdown returns to the kiosk. The student chrome remains a three-step flow.

#### 4.3.2 Staff Module

The Staff Module is used by authorized staff assigned to one window. Staff see the waiting list in fair call order, the held list, the current number, the student name or Walk-in (reason) and screen token, and the window timer. They can Call Next, Recall, Complete, Hold, and Call Held. Hold finishes the open call, sets status to held, frees the window, and removes the number from Now Serving and waiting lists. Call Held resumes a held ticket at that window without 2 Priority → 1 Regular and requires no open serving ticket. Concurrent Call Next actions on shared services do not assign the same ticket twice. Staff history shows the user’s own done tickets. A paper-out alert appears on the staff window and System Float when the thermal printer has no paper.

#### 4.3.3 Administrator Module

The Administrator Module provides live totals, waiting information, user management, student add/delete/CSV import, RFID/NFC card UID and mobile enrollment, kiosk settings (walk-in PIN and printer paper SMS), walk-in issue, history, all tickets including held, and overall and per-window average waiting and service times. Reports use the latest completed call of a done ticket. Hold sessions are not counted as served. There is no public Services CRUD screen; office and window records are configured as part of the deployed domain data. The Guard role cannot log in.

#### 4.3.4 Queue Display Module

The Queue Display Module groups windows and shows Now Serving and waiting numbers only. Waiting rows use the same fair order as Call Next, with a maximum of 10 rows. Held tickets and student names are omitted. Voice announcement may be enabled in the browser.

### 4.4 Database Implementation

The system uses a MySQL/MariaDB database. Domain tables are services, windows, users, students, settings, paper_sms_contacts, daily_queue_counters, queues, and queue_calls.

**Table 4.1 Implemented Domain Tables**


| Table                | Purpose                                                                                                   |
| -------------------- | --------------------------------------------------------------------------------------------------------- |
| services             | Service name and ticket prefix                                                                            |
| windows              | Counter name, display group, assigned service, and status                                                 |
| users                | Admin, staff, and guard accounts; staff window assignment is unique                                       |
| students             | Authorized Student ID, name, optional RFID/NFC serial (UID), optional mobile                              |
| settings             | Kiosk walk-in PIN (`walkin_pin`)                                                                          |
| paper_sms_contacts   | Staff or assistant mobiles; enabled means Send SMS for the paper-out text                                 |
| daily_queue_counters | Last number per service and date                                                                          |
| queues               | Ticket number, service, optional student or issuer/reason, optional screen token and SMS number, priority, status, date |
| queue_calls          | Window, called time, and finished time                                                                    |


Queue status values used in operation are waiting, serving, held, done, and cancelled. Walk-in tickets leave `student_id` empty and store `issued_by`, `issue_reason`, and `screen_token`. The student name is never stored on the ticket row. Reports count done tickets only.

### 4.5 User Interface Presentation

The system provides separate interfaces by role.

The kiosk uses a compact three-step layout suitable for a 1024×600 screen: service, priority, and confirmation with summary, Student ID keypad, and RFID/NFC reader. A small corner control opens the PIN pad for walk-in issuance. After a walk-in prints, the cooldown screen shows the number and the screen token.

The staff panel shows the current ticket, name or Walk-in (reason) and screen token, timer, fair waiting list, held list, Call Next, Recall, Complete, Hold, Call Held, and a paper-out alert when the thermal printer has no paper. An optional System Float window can stay on top on a staff PC and shows the same alert.

The public display shows grouped windows, Now Serving, and waiting numbers only.

The administrator dashboard shows live totals, users, students, kiosk settings (walk-in PIN and printer paper SMS), walk-in issue, history, tickets, and done-only reports.

Screenshots placed in this section are placeholders and must be replaced with captures of the current kiosk steps (including confirm keypad and RFID/NFC tap), number-only 58mm ticket, staff panel, public display, and administrator dashboard. The descriptions above match the implemented system. Diagrams may be inserted later.

### 4.6 System Implementation

The system was implemented with PHP 8.2 or higher, Laravel 12, Blade, Vite, Tailwind CSS, and MySQL/MariaDB on Windows/XAMPP on the mini PC (Intel Core i5 8th Generation, 8 GB RAM). That mini PC is both the kiosk host and the application server. Local assets are bundled so the interfaces do not depend on a CDN. The kiosk, XP-Q90EC printer, ACR122U-A9 reader, SIM7600G-H module, public display, staff PCs, administrator PC, and database communicate on the LAN [11].

The student path is service → priority → type or tap Student ID confirm → optionally turn on SMS (stored mobile) → generate → print → wait on the display → staff call → serve or hold → complete. Optional GSM SMS is queued after print, at 10 or fewer ahead, and when called. The walk-in path substitutes PIN unlock, reason, and optional overlay mobile for Student ID. FairQueueScheduler applies 2 Priority → 1 Regular on shared services. WaitTimeEstimator supplies an estimate only when enough completed samples exist. StudentQueueGuard enforces the one-open-student-ticket-per-day rule. KioskWalkInGate checks the PIN on the server.

Staff actions update `queue_calls` and queue status. Hold is not Complete. Administrators maintain users, students, and the walk-in PIN, and review done-only reports. The implementation therefore matches the standalone localized deployment described for campus information systems [9].

### 4.7 Testing Results



#### 4.7.1 Functional Testing

Functional testing checked whether the major functions met the documented requirements.

**Table 4.2 Functional Testing Results**


| Function tested            | Expected result                                                                                                                                                                                                                      | Result |
| -------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ | ------ |
| Public service selection   | Cashier, Registrar, and DMO appear; Promissory Notes is hidden                                                                                                                                                                       | Passed |
| Priority selection         | Regular or Priority is stored                                                                                                                                                                                                        | Passed |
| Student ID confirmation    | Unknown ID or unknown card is rejected; Confirm stays disabled until lookup succeeds; keypad remains available without a card                                                                                                        | Passed |
| One open student ticket    | A second open ticket for the same ID on the same day is rejected                                                                                                                                                                     | Passed |
| Walk-in PIN                | Wrong PIN is rejected; correct PIN allows issue without Student ID                                                                                                                                                                   | Passed |
| RFID/NFC tap               | Enrolled card on the ACR122U-A9 fills confirm; unenrolled card is rejected; service and priority were already selected                                                                                                               | Passed |
| Queue generation and print | A unique daily number is stored and printed without a name                                                                                                                                                                           | Passed |
| Paper-out alert            | Staff window and System Float show an alert when the thermal printer has no paper; checked staff numbers may receive one SMS; stored ticket remains valid                                                                            | Passed |
| Fair Call Next             | 2 Priority → 1 Regular; two cashier windows do not claim the same ticket                                                                                                                                                             | Passed |
| GSM SMS                    | Ticket, 10-away, and called texts go only to tickets that opted in (stored student mobile, or walk-in Philippine number and screen token); name omitted; queue still runs if the SIM7600G-H modem is unplugged; Confirm still prints when no number is on file | Passed |
| Hold and Call Held         | Hold removes the number from Now Serving; Call Held resumes it; hold is not done                                                                                                                                                     | Passed |
| Public display             | Serving and waiting numbers only; held tickets and names omitted                                                                                                                                                                     | Passed |
| Administrator functions    | Users, students, kiosk PIN, paper SMS list, walk-in issue, history, and done-only reports                                                                                                                                            | Passed |


The tested functions therefore covered the kiosk, Student ID keypad and ACR122U-A9 RFID/NFC tap, walk-in rules, printing, paper-out alert, optional GSM SMS through the SIM7600G-H module, fair scheduling, staff controls, display privacy, and administration.

#### 4.7.2 Non-Functional Testing

Non-functional testing assessed usability, efficiency, reliability and accuracy, security, compatibility, and local-network operation.

The system processed kiosk requests, printed tickets, updated staff and display views, and recorded calls during testing on the LAN. Interfaces remained a three-step student path and a role-based panel for staff and administrators. The walk-in PIN was checked on the server. The Guard role could not log in. No percentage reduction in waiting time and no fixed response-time benchmark are claimed, because the study does not report a controlled before-and-after operational measurement.

#### 4.7.3 User Acceptance and Expert Evaluation

The Chapter 3 student-and-parent questionnaire measured the current line and the proposed kiosk before development. The results in this section are evaluation results of the implemented system. They are not mixed with Tables 3.1–3.3.

Evaluation after testing used five instruments. All used the same five-point scale: 5 Strongly Agree, 4 Agree, 3 Neutral, 2 Disagree, and 1 Strongly Disagree. Weighted means used the same formula and interpretation ranges as Chapter 3.

The Technical Expert System Evaluation Form was completed by one IT expert who tested the system. Expert ratings are not combined with the user means. User Acceptance Testing used Cashier, Registrar, and DMO staff forms (N = 1 each) and the Student System Feedback Form (N = 30).

The five instruments and their scored sections are:


| Instrument                              | Respondents                                      | Sections scored                                                                                                                       | Items |
| --------------------------------------- | ------------------------------------------------ | ------------------------------------------------------------------------------------------------------------------------------------- | ----- |
| Technical Expert System Evaluation Form | 1 IT expert                                      | Functionality; Usability; Efficiency; Reliability and Accuracy; User Satisfaction                                                     | 25    |
| Cashier System Feedback Form            | Cashier staff (N = 1)                            | Functionality; Queue Management and Multi-Counter Operation; Usability and Efficiency; Reliability and Accuracy; Overall Satisfaction | 25    |
| Registrar System Feedback Form          | Registrar staff (N = 1)                          | Functionality; Queue Management; Usability and Efficiency; Reliability and Accuracy; Overall Satisfaction                             | 25    |
| DMO System Feedback Form                | DMO staff (N = 1)                                | Functionality; Queue Management; Usability and Efficiency; Reliability and Accuracy; Overall Satisfaction                             | 25    |
| Student System Feedback Form            | Students, parents, and walk-in visitors (N = 30) | Functionality; Usability; Efficiency; Reliability and Accuracy; User Satisfaction                                                     | 27    |


The Student System Feedback Form was administered to **20 students**, **5 parents**, and **5 walk-in visitors** at PECIT (N = 30). Student section means are reported in Table 4.9. The IT expert results are reported in Table 4.4.

##### 4.7.3.1 Combined User Acceptance Results

**Table 4.3 User Acceptance Evaluation (office staff and student form; IT expert in Table 4.4 only)**


| Evaluation Area          | Mean | Interpretation |
| ------------------------ | ---- | -------------- |
| Functionality            | 4.93 | Strongly Agree |
| Usability                | 4.98 | Strongly Agree |
| Efficiency               | 4.97 | Strongly Agree |
| Reliability and Accuracy | 4.93 | Strongly Agree |
| User Satisfaction        | 4.99 | Strongly Agree |
| Overall Mean             | 4.96 | Strongly Agree |


Table 4.3 combines Cashier, Registrar, DMO, and Student System Feedback Form means. Functionality uses office Section A and student Section A; Usability uses office Section C and student Section B; Efficiency uses office Section C and student Section C; Reliability and Accuracy uses office Section D and student Section D; User Satisfaction uses office Section E and student Section E. The IT expert score is kept in Table 4.4 only. Efficiency is a perception score, not a measured cut in waiting time.

##### 4.7.3.2 Technical Expert Evaluation

**Table 4.4 Technical Expert System Evaluation (N = 1)**


| Evaluation Area          | Items | Mean | Interpretation |
| ------------------------ | ----- | ---- | -------------- |
| Functionality            | 5     | 5.00 | Strongly Agree |
| Usability                | 5     | 5.00 | Strongly Agree |
| Efficiency               | 5     | 5.00 | Strongly Agree |
| Reliability and Accuracy | 5     | 5.00 | Strongly Agree |
| User Satisfaction        | 5     | 5.00 | Strongly Agree |
| Overall Mean             | 25    | 5.00 | Strongly Agree |


All 25 IT expert items were rated 5 (Strongly Agree), for section means of 5.00 and an overall mean of 5.00, Strongly Agree. The expert stated that the system is ready and can be deployed in PECIT, that network configuration must be set up because PECIT uses a cloud-based network, and that the UI design should be modernized. The expert score is not mixed into Table 4.3.

##### 4.7.3.3 Results by Feedback Form

**Table 4.5 Overall Means by Feedback Form**


| Respondent group                        | Instrument                     | Items | N   | Overall mean | Interpretation |
| --------------------------------------- | ------------------------------ | ----- | --- | ------------ | -------------- |
| Cashier staff                           | Cashier System Feedback Form   | 25    | 1   | 5.00         | Strongly Agree |
| Registrar staff                         | Registrar System Feedback Form | 25    | 1   | 5.00         | Strongly Agree |
| DMO staff                               | DMO System Feedback Form       | 25    | 1   | 4.92         | Strongly Agree |
| Students, parents, and walk-in visitors | Student System Feedback Form   | 27    | 30  | 4.91         | Strongly Agree |


**Table 4.6 Cashier System Feedback Form — Section Means (N = 1)**


| Section                                         | Items | Mean | Interpretation |
| ----------------------------------------------- | ----- | ---- | -------------- |
| A. Functionality                                | 5     | 5.00 | Strongly Agree |
| B. Queue Management and Multi-Counter Operation | 5     | 5.00 | Strongly Agree |
| C. Usability and Efficiency                     | 5     | 5.00 | Strongly Agree |
| D. Reliability and Accuracy                     | 5     | 5.00 | Strongly Agree |
| E. Overall Satisfaction                         | 5     | 5.00 | Strongly Agree |
| Overall Mean                                    | 25    | 5.00 | Strongly Agree |


All 25 Cashier items were rated 5 (Strongly Agree). The Cashier evaluator liked that the queuing system organizes students and parents efficiently and makes the cashier process more orderly. The main difficulty noted was getting familiar with the system at first. No further improvement was suggested.

**Table 4.7 Registrar System Feedback Form — Section Means (N = 1)**


| Section                     | Items | Mean | Interpretation |
| --------------------------- | ----- | ---- | -------------- |
| A. Functionality            | 5     | 5.00 | Strongly Agree |
| B. Queue Management         | 5     | 5.00 | Strongly Agree |
| C. Usability and Efficiency | 5     | 5.00 | Strongly Agree |
| D. Reliability and Accuracy | 5     | 5.00 | Strongly Agree |
| E. Overall Satisfaction     | 5     | 5.00 | Strongly Agree |
| Overall Mean                | 25    | 5.00 | Strongly Agree |


All 25 Registrar items were rated 5 (Strongly Agree). The Registrar evaluator stated that the system is very easy to use. No problems or further suggestions were reported on the form.

**Table 4.8 DMO System Feedback Form — Section Means (N = 1)**


| Section                     | Items | Mean | Interpretation |
| --------------------------- | ----- | ---- | -------------- |
| A. Functionality            | 5     | 4.80 | Strongly Agree |
| B. Queue Management         | 5     | 5.00 | Strongly Agree |
| C. Usability and Efficiency | 5     | 5.00 | Strongly Agree |
| D. Reliability and Accuracy | 5     | 4.80 | Strongly Agree |
| E. Overall Satisfaction     | 5     | 5.00 | Strongly Agree |
| Overall Mean                | 25    | 4.92 | Strongly Agree |


In Sections A and D, four items were rated Strongly Agree (5) and one item Agree (4), for a section mean of 4.80. Sections B, C, and E were all Strongly Agree (5.00). The overall mean is 4.92, Strongly Agree. The DMO evaluator liked the system, reported no problems so far, and suggested considering branding or logo placement and optional dark mode for the staff interface.

**Table 4.9 Student System Feedback Form — Section Means (N = 30)**


| Section                     | Items | Mean | Interpretation |
| --------------------------- | ----- | ---- | -------------- |
| A. Functionality            | 6     | 4.90 | Strongly Agree |
| B. Usability                | 6     | 4.93 | Strongly Agree |
| C. Efficiency               | 5     | 4.88 | Strongly Agree |
| D. Reliability and Accuracy | 5     | 4.90 | Strongly Agree |
| E. User Satisfaction        | 5     | 4.95 | Strongly Agree |
| Overall Mean                | 27    | 4.91 | Strongly Agree |


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

Objective 3. Each confirmed request produced a 58mm number-only ticket. If the printer was out of paper, the staff window and System Float showed a paper-out alert, checked staff numbers could receive one SMS, and the stored ticket remained valid. Call Next applied 2 Priority → 1 Regular across shared windows, including Cashier 1–3, without assigning the same ticket twice.

Objective 4. The public display and staff panel showed current serving and waiting numbers in the same fair order. Held tickets and names were omitted from the public view. Optional local GSM SMS through the SIM7600G-H module notified an opted-in ticket (stored student mobile, or walk-in Philippine number and screen token) when the ticket was printed, when it first had 10 or fewer ahead, and when it was called.

Objective 5. Functional tests passed for the implemented rules. Cashier and Registrar scored **5.00** overall, DMO scored **4.92**, and the Student System Feedback Form scored **4.91** overall, all **Strongly Agree**. The combined user mean in Table 4.3 is **4.96**, **Strongly Agree**. The IT expert scored **5.00** overall in Table 4.4 and recommended deployment readiness, network setup for PECIT's cloud-based network, and UI modernization.

Taken together, the implementation and evaluation show that the standalone kiosk system can replace the manual line with an organized, monitored, and recorded queue process at the selected PECIT offices. The study does not claim a measured percentage reduction in waiting time.

# CHAPTER 5



## SUMMARY, CONCLUSIONS, AND RECOMMENDATIONS

This chapter summarizes the study, states the conclusions that answer the five specific objectives, and presents recommendations for PECIT, later system work, and future researchers.

### 5.1 Summary

This study designed, developed, and implemented a Standalone Kiosk-Based Queuing Management System with Thermal Printer for the Cashier, Registrar, and Data Management Office (DMO) of the Philippine Electronics and Communication Institute of Technology (PECIT). The offices previously used a manual first-come-first-served line. That process produced unclear order, line cutting, difficulty serving priority clients, crowding, and no shared view of who was waiting.

The implemented system is standalone and LAN-based [11]. One mini PC (Intel Core i5 8th Generation, 8 GB RAM) is both the public kiosk host and the application server. A student uses three kiosk steps: service, Regular or Priority, and confirmation with a validated Student ID typed on the keypad or tapped on an enrolled RFID/NFC card at the ACR122U-A9 reader. Clients who do not bring the card may still type the Student ID. Clients who cannot use a Student ID may request a walk-in ticket after the kiosk PIN stored in settings is accepted. After lookup, **Notify me by SMS** is off by default and uses the mobile number stored on the student record; students do not type a number. If no mobile is on file, the control stays off and disabled and shows **No SMS number on file**; print still proceeds. Walk-in clients may leave the mobile blank or enter a Philippine number on the PIN overlay, and the cooldown screen shows a token such as T1006ABC. The system prints a 58mm number-only ticket on the XP-Q90EC printer, may send local GSM SMS through the SIM7600G-H module after print, when the ticket first has 10 or fewer ahead, and when it is called, applies 2 Priority → 1 Regular across shared windows, and shows serving and waiting numbers on a public display. When the printer is out of paper, the staff window and System Float show a paper-out alert, and checked staff or assistant numbers may receive one SMS. Staff can Call Next, Recall, Complete, Hold, and Call Held. Administrators manage users and students, enroll card serials (UID) and mobile numbers, set the walk-in PIN, may issue walk-in tickets, and generate reports from done tickets only. The Guard role cannot log in. Student names are not printed, shown on the public display, or included in SMS. The kiosk does not show the full student mobile number.

Development followed the Waterfall model: Requirements Analysis, System Design, Implementation and Development, Testing, and Deployment [19]. The four modules are the Student Kiosk Module, Staff Module, Administrator Module, and Queue Display Module.

Chapter 4 reported that the tested functions operated as intended. Cashier and Registrar scored **5.00** overall, DMO scored **4.92**, and the Student System Feedback Form scored **4.91** overall, all **Strongly Agree**. The combined user mean in Table 4.3 is **4.96**, **Strongly Agree**. The IT expert scored **5.00** overall in Table 4.4. The study does not claim a measured percentage reduction in waiting time.

### 5.2 Conclusions

Based on the development, implementation, testing, and evaluation, the researchers conclude the following:

1. A standalone kiosk-based queue process can organize Cashier, Registrar, and DMO transactions that previously depended on a manual first-come-first-served line. Shared service queues and per-window timers allow several cashiers to serve one waiting list without assigning the same ticket twice.
2. Confirmation before numbering is workable when the request is checked through Student ID validation by keypad or RFID/NFC tap, or authorized kiosk-PIN walk-in issuance. Unknown IDs, unknown cards, and a second open student ticket on the same day are rejected. Print, display, and SMS remain number-only.
3. The 58mm thermal printer gives the client a physical queue reference after confirmation. If the printer is out of paper, the staff window and System Float show a paper-out alert, checked staff numbers may receive one SMS, and the stored ticket remains valid. 2 Priority → 1 Regular calling serves priority clients fairly while multiple windows share one service queue.
4. The public display and staff panel provide a real-time view of who is being served and who is waiting. Waiting rows follow the same fair order as Call Next. Held tickets and student names are omitted from the public view. Optional local GSM SMS through the SIM7600G-H module notifies an opted-in ticket (stored student mobile, or walk-in Philippine number and screen token) after print, when the ticket first has 10 or fewer ahead, and when it is called.
5. The system can be evaluated through functional testing, expert review, and User Acceptance Testing. User feedback was **Strongly Agree** (Cashier **5.00**, Registrar **5.00**, DMO **4.92**, Student form **4.91**; combined **4.96**). The IT expert scored **5.00** overall under ISO/IEC 25010-aligned characteristics [10].

The developed system is therefore a practical replacement for the manual line at the selected PECIT offices. The study does not claim a measured numerical reduction in actual waiting time because no controlled before-and-after operational measurement is reported.

### 5.3 Recommendations

The researchers recommend the following:

1. PECIT may deploy the system in the Cashier, Registrar, and DMO waiting areas using the mini PC (Intel Core i5 8th Generation, 8 GB RAM) as both kiosk and server, the 58mm XP-Q90EC printer, ACR122U-A9 RFID/NFC reader, SIM7600G-H GSM/LTE module, public display, staff PCs, and LAN already specified for standalone operation. Before go-live, network configuration should be prepared for PECIT's cloud-based network environment, as noted by the IT expert.
2. Administrators and staff should receive orientation on the three-step kiosk, Student ID and card-tap errors, walk-in PIN use and the cooldown screen token, GSM SMS (**Notify me by SMS** off by default; stored student mobile; walk-in Philippine number and screen token), paper-out alerts on the staff window and System Float, with optional staff SMS, Call Next, Recall, Complete, Hold, and Call Held before the offices rely on the system during peak hours.
3. The mini PC, printer, RFID/NFC reader, GSM module and SIM load, display, workstations, database, and LAN should be checked regularly. Spare paper should be kept at the kiosk. The staff window and System Float paper-out alert, and the optional staff SMS, should be treated as a signal to reload the printer so stored tickets can still be reprinted.
4. Administrators should keep the student allowlist, enrolled card serials (UID), and mobile numbers current, change the walk-in PIN when needed, and keep backups of the local database. The PIN should remain server-checked and should not be placed on the public kiosk page.
5. Staff and administrators should review done-only reports and held tickets to see where windows need help, without treating Hold as a completed service.
6. After full use, the offices should record actual waiting and service times before and after deployment if later studies need a measured efficiency comparison.



### 5.4 Future Enhancements

The current system is limited to the standalone LAN environment in the scope of the study. Later versions may add features that are not implemented now:

1. A modernized staff and kiosk UI design, as recommended by the IT expert, while keeping the current three-step kiosk flow and number-only print, display, and SMS rules.
2. A QR code on the ticket that opens number-only queue status.
3. A remote reservation option that still requires Student ID validation or authorized walk-in issuance and does not replace the on-site kiosk.
4. Additional campus offices, if they can share the same local rules without requiring continuous internet connectivity.
5. More detailed analytics of volume, wait, and service time from done tickets only.
6. Off-site backup for institutions that later accept that extra connectivity, while daily queue operations remain local [11].

These items are future work. They are not part of the implemented PECIT kiosk system. RFID/NFC tap at confirm, the walk-in screen token, and local GSM SMS (ticket text, 10-away come-back, and called window) are part of the current build.

### 5.5 Recommendations for Future Researchers

Future researchers may evaluate the system with larger user groups and longer operational periods, and they may compare 2 Priority → 1 Regular with other call orders under measured wait and service times. Studies may also examine walk-in volume versus student-ID tickets, the effect of Hold and Call Held on window use, and whether estimated wait matches later completed times.

Researchers who add a cloud SMS API, remote reservation, or cloud monitoring should keep the privacy rule used here: Student ID is for confirmation and staff serving; names are not printed, shown on the public display, or included in SMS. Localized campus information systems remain a suitable base for that work [9].

Future researchers are encouraged to keep the standalone kiosk, thermal ticket, and fair shared-window design while collecting the operational measurements this study does not claim.

# REFERENCES

[1] Akila, I. S., Pratheek, P., & Poonia, A. (2023). Smart identity card for campus automation. In *2023 Innovations in Power and Advanced Computing Technologies (i-PACT)* (pp. 1–5). IEEE. [https://doi.org/10.1109/I-PACT58649.2023.10434674](https://doi.org/10.1109/I-PACT58649.2023.10434674)

[2] Ariningsih, P., & Muhammad, A. H. (2024). Quality evaluation of ticketing management system using ISO/IEC 25010:2023 standards and AHP method. *Intechno Journal: Information Technology Journal, 6*(2). [https://doi.org/10.24076/intechnojournal.2024v6i2.1870](https://doi.org/10.24076/intechnojournal.2024v6i2.1870)

[3] Barbado, R. M. B., Peñaflorida, L. A. E., Taclibon, W., Temprosa, H. Q., & Austria, Y. D. (2025). ADUPay: A self-service payment kiosk for Adamson University’s recognized student organization. In *2025 IEEE Malaysia International Conference on Communications (MICC)*. IEEE. [https://doi.org/10.1109/MICC66164.2025.11210991](https://doi.org/10.1109/MICC66164.2025.11210991)

[4] Caranza, A. C. O., Cortez, C. C. G., Da, L. V., Dela Cruz, S. A. S., Rapadas, D. M., Santos, M. N. M., & Naturales, J. D. (2026). Enhancing student service efficiency through a smart enrollment queuing system for St. Clare College of Caloocan. *International Journal of Research and Innovation in Applied Science, 11*(5), 1493–1526. [https://doi.org/10.51584/ijrias.2026.11050127](https://doi.org/10.51584/ijrias.2026.11050127)

[5] Commission on Higher Education Regional Office XI. (2024, July 1). *CHED RO XI awarded as government champion for digital transformation at DEVCON Mindanao Summit 2024*. [https://ro11.ched.gov.ph/2024/07/01/ched-ro-xi-awarded-as-government-champion-for-digital-transformation/9085/press-release/](https://ro11.ched.gov.ph/2024/07/01/ched-ro-xi-awarded-as-government-champion-for-digital-transformation/9085/press-release/)

[6] Concepcion, A. T. V., Concepcion, A. U., Guinto, C. A. A., Araza, C., Imperio, R. D. H., Regaas, B., & Perez, N. M. N. (2026). Smart queuing and appointment management system for client services in Bulacan State University Bustos Campus. *International Journal of Multidisciplinary: Applied Business and Education Research, 7*(1), 5–14. [https://doi.org/10.11594/ijmaber.07.01.02](https://doi.org/10.11594/ijmaber.07.01.02)

[7] Espinosa, G. S., Serrano, M. R., & Balmes, H. A. (2024). Automated enrollment queueing system in a university towards student experience and operational efficiency. *MSEUF Research Studies, 21*(1). [https://www.ejournals.ph/article.php?id=24562](https://www.ejournals.ph/article.php?id=24562)

[8] Falcon, I. G., Dugaria, K. J. L., & Remojo, D. C. (2026). A three-tier integrated queue management system: Optimized service delivery for land transportation clients. *Journal of Engineering and Technology for Industrial Applications, 12*(57), 559–566. [https://doi.org/10.5935/jetia.v12i57.2567](https://doi.org/10.5935/jetia.v12i57.2567)

[9] Grepon, B. G. S., Baran, N. T., Gumonan, K. M. V. C., Martinez, A. L. M., & Lacsa, M. L. E. (2022). Designing and implementing e-school systems: An information systems approach to school management of a community college in Northern Mindanao, Philippines. *International Journal of Computing Sciences Research, 6*, 792–808. [https://doi.org/10.25147/ijcsr.2017.001.1.74](https://doi.org/10.25147/ijcsr.2017.001.1.74)

[10] International Organization for Standardization. (2023). *Systems and software engineering—Systems and software Quality Requirements and Evaluation (SQuaRE)—Product quality model* (ISO/IEC 25010:2023). [https://www.iso.org/standard/78176.html](https://www.iso.org/standard/78176.html)

[11] Linhalis, F., & Silva, A. C. (2023). Solutions to enable the use of ICT in education in regions with low internet connectivity: A literature review. *Brazilian Journal of Education, Technology and Society, 16*(4), 1185–1196. [https://doi.org/10.14571/brajets.v16.n4.1185-1196](https://doi.org/10.14571/brajets.v16.n4.1185-1196)

[12] Maala, R. F., Sebua, N. B., & Evangelista, K. L. L. (2023). Queuing management system in Manuel S. Enverga University Foundation Candelaria Inc. *International Journal of Advanced Research in Computer Science, 14*(6), 44–53. [https://doi.org/10.26483/ijarcs.v14i6.7039](https://doi.org/10.26483/ijarcs.v14i6.7039)

[13] Mallari, M. L. Z., Guintu, J. S., Magalong, Y. C., & Yap, D. S. (2022). CLIQUE: A web-based queue management system with real-time queue tracking and notification of units for Angeles University Foundation Office of the University Registrar. In *Proceedings of the 12th Annual International Conference on Industrial Engineering and Operations Management*. IEOM Society. [https://doi.org/10.46254/an12.20220359](https://doi.org/10.46254/an12.20220359)

[14] National Privacy Commission. (2012). *Implementing rules and regulations of Republic Act No. 10173*. [https://privacy.gov.ph/data-privacy-act/](https://privacy.gov.ph/data-privacy-act/)

[15] Panganiban, J. F., & Becenia, A. B. (2026). iSkulFlow: An event-driven queueing and transactional management system for registrar and financial services. *International Journal of Education, Research, Innovation Perspectives*. [https://doi.org/10.5281/zenodo.19020670](https://doi.org/10.5281/zenodo.19020670)

[16] Pantonial, L., Geroy, C., & Prado, N. (2025). Student payment behaviors, queue management, and cashier efficiency in a private higher education institution. *Psychology and Education: A Multidisciplinary Journal, 36*(9), 1005–1010. [https://doi.org/10.70838/pemj.360905](https://doi.org/10.70838/pemj.360905)

[17] Ramoga, M. R. (2025). Ticketless ID operated queue management optimization with SMS notification and voiceover feature. *Current Science Research Bulletin*. [https://doi.org/10.5281/zenodo.16926252](https://doi.org/10.5281/zenodo.16926252)

[18] Republic of the Philippines. (2012). *Republic Act No. 10173: Data Privacy Act of 2012*. Official Gazette. [https://www.officialgazette.gov.ph/2012/08/15/republic-act-no-10173/](https://www.officialgazette.gov.ph/2012/08/15/republic-act-no-10173/)

[19] Saravanos, A., & Curinga, M. X. (2023). Simulating the software development lifecycle: The waterfall model. *Applied System Innovation, 6*(6), Article 108. [https://doi.org/10.3390/asi6060108](https://doi.org/10.3390/asi6060108)

[20] Sumalinog, P. S., Jr., & Mahusay, G. S. (2025). Q-Line UCLM: Enhancing student service flow through paperless. *UCLM Journal of Research and Innovation, 1*(1). [https://doi.org/10.5281/zenodo.19181459](https://doi.org/10.5281/zenodo.19181459)

[21] Tesa, C. A., Haris, M. S., & Kusuma, W. T. (2025). An analysis of digital queuing systems in Indonesian banking services: A systematic literature review. *International Journal of Software Engineering and Computer Science, 5*(2). [https://doi.org/10.35870/ijsecs.v5i2.4379](https://doi.org/10.35870/ijsecs.v5i2.4379)

[22] UNESCO. (2023). *Global education monitoring report 2023: Technology in education: A tool on whose terms?* [https://doi.org/10.54676/uzqv8501](https://doi.org/10.54676/uzqv8501)

# CURRICULUM VITAE

Each researcher has a one-page curriculum vitae in the Word manuscript. Leave the 2 in. × 2 in. photograph box empty until a picture is attached. Fill in birth date, address, contact number, email, secondary school, and seminars on that page.

## RHEM A. SUMODLAYON

Lead Developer / System Architect. Backend, queue logic, staff tools, and deployment. Photograph to be attached.

## MIKE D. BEDRONA

Frontend / UI Designer. Kiosk, public display, and panel interface design. Photograph to be attached.

## NIÑO ANGELO M. MALICAY

Research / Documentation Lead. Manuscript, diagrams, and presentation. Photograph to be attached.

## OMAR O. BAYABAO

Database / QA Specialist. Schema, testing, documentation, and validation. Photograph to be attached.

## RON MATTHEW F. ARQUISOLA

Hardware / Deployment Specialist. Kiosk PC, thermal printer, and display launchers. Photograph to be attached.