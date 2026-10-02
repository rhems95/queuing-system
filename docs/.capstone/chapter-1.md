# CHAPTER 1

## INTRODUCTION

### 1.1 Background of the Study

Efficient service delivery is an important aspect of institutional operations, particularly in offices that handle a high volume of transactions within limited periods. In educational institutions, student service offices commonly manage activities such as payments, enrollment-related concerns, academic records, document requests, and verification. At PECIT, these transactions are currently handled through manual first-come-first-served lining. When clients depend only on physical arrival order, they may experience long lines, uncertainty about service order, overcrowded waiting areas, line cutting, and difficulty determining when their turn will be served [12]. Administrative personnel may likewise experience challenges in coordinating clients, serving priority cases, and monitoring the movement of transactions across several windows.

The development of digital technologies has encouraged organizations and educational institutions to adopt computerized systems that improve the organization and delivery of services. In the Philippine higher education sector, digital transformation has become increasingly relevant to institutional support services. The Commission on Higher Education Regional Office XI [5] has implemented and promoted digital transformation initiatives involving higher education institutions, including automated frontline services and a queueing system with online appointment and automated printing capabilities. These developments demonstrate the growing relevance of technology-assisted processes in improving administrative service delivery.

Self-service kiosk technology provides another approach for organizing the initial stage of service transactions. Kiosks allow clients to interact directly with a computerized interface to select a service and initiate a transaction. In campus service settings, digitized queue entry has been used to standardize how clients join a line, obtain a queue reference, and reduce dependence on informal physical lining [12]. In a student service environment, a kiosk can provide a structured process for selecting a service, choosing a queue type, confirming the request, and receiving a printed queue ticket.

Automated queueing has also been applied specifically in Philippine higher education institutions. Espinosa et al. [7] developed an automated enrollment queueing system for a university to address problems related to student waiting lines, overcrowding, and employee workload. Their study reported that an automated queueing approach could provide a more organized and controlled enrollment process and improve the experience of students and guardians. Pantonial et al. [16] examined queue management and cashier efficiency in a private higher education institution and highlighted the relationship between queue conditions, student payment behavior, and cashier productivity. Ramoga [17] also examined a queue management system for the Registrar and Cashier offices of Jose Rizal Memorial State University–Dipolog Campus, addressing manual queuing problems such as long waiting times, crowded service areas, and communication gaps. These studies establish that queue management remains a relevant concern in Philippine higher education service environments.

Despite these developments, existing queue management implementations differ in their architecture, scope, and combination of functions. Some systems focus on enrollment or specific office processes, while others use online, appointment-based, ticketless, or hybrid approaches [7], [17]. This indicates a continuing need for queue management solutions that are designed around the particular operational conditions of an institution. UNESCO [22] emphasizes that educational technology should be selected according to relevance, institutional context, and sustainability rather than adopted merely because it is available. For PECIT, such a solution needs to coordinate multiple student service offices and service windows while providing a clear process for queue generation, priority handling, ticket issuance, queue monitoring, staff control, and administrative reporting.

A standalone, Local Area Network (LAN)-based architecture is appropriate for an institutional environment where continuous internet connectivity is not required for essential queue operations [22], [9]. In the developed system, the kiosk, staff workstations, public display, administrator computer, and local database communicate within the institution's local network. The system is built using Laravel 12, Blade, Vite/Tailwind, and MySQL/MariaDB and is intended for local deployment through the institution's available computing and network resources.

The proposed system is specifically designed for the student service offices of the Philippine Electronics and Communication Institute of Technology (PECIT). Through the touchscreen kiosk, clients select a service, choose either Regular or Priority, and proceed to a confirmation stage. During confirmation the Student ID is typed on the keypad or read from an enrolled RFID/NFC student ID card at the kiosk reader, then validated before a queue ticket is generated. Clients who do not bring the physical ID may still type the Student ID. Clients who cannot use a Student ID may request a walk-in ticket at the same kiosk after entering the designated kiosk PIN. When sufficient historical and current queue information is available, the confirmation stage may also provide an estimated waiting time. A **Notify me by SMS** control is **off by default**. Students do not type a mobile number at the kiosk; notices use the mobile number stored with the Student ID and RFID/NFC card serial in the student list. Walk-in clients have no student record and may enter a mobile number only on the PIN overlay. The student kiosk flow remains a three-step process of service selection, priority selection, and confirmation before ticket generation.

The staff component supports coordinated queue management across service windows. Multiple windows assigned to the same service can share one waiting queue. In the Cashier service, for example, Cashier 1, Cashier 2, and Cashier 3 share a common Cashier waiting queue while maintaining independent active transactions and service timers. The system applies a service-wide 2 Priority → 1 Regular scheduling rule and provides authorized staff with functions to call the next queue, recall a queue, place a transaction on hold, resume a held transaction, and complete a transaction. While serving, staff may view the student name associated with a validated Student ID or the walk-in reason for an authorized walk-in ticket.

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
4. How can clients and service windows track who is being served and who is waiting when the current process has no real-time queue display, and how can a student who opts in to SMS—or a walk-in who gives a mobile number—be notified when their ticket is near and when it is called?
5. How can the developed system be evaluated in terms of functionality, usability, efficiency, reliability and accuracy, and user satisfaction through system testing, expert evaluation, and User Acceptance Testing (UAT)?



### 1.4 Objectives of the Study



#### 1.4.1 General Objective

The **general objective** of this study is to design, develop, and implement a **Standalone Kiosk-Based Queuing Management System with Thermal Printer** for the student service offices of the Philippine Electronics and Communication Institute of Technology (PECIT) to automate queue processing, organize service transactions, improve queue monitoring and coordination, and support efficient service delivery.

#### 1.4.2 Specific Objectives

Specifically, this study aims to:

1. Develop a standalone kiosk-based queuing management system that automates and organizes student transactions for the selected PECIT student service offices, particularly the Cashier, Registrar, and Data Management Office (DMO).
2. Implement a confirmation-based transaction process that allows clients to select a service, choose Regular or Priority queue status, type or tap and validate their Student ID or complete authorized kiosk-PIN walk-in issuance, and confirm the request before a queue number is generated.
3. Integrate a **58mm USB thermal printer** that automatically produces a physical queue ticket containing the necessary queue information after a confirmed transaction, and incorporate a priority queue mechanism that follows the **2 Priority → 1 Regular** scheduling rule while supporting multiple service windows assigned to the same service.
4. Implement a real-time queue display and monitoring feature that allows clients and authorized staff to view current queue information, supports coordination among service windows, and sends optional local GSM SMS notices when a ticket is five places away in fair call order and when the number is called. Students opt in with an SMS toggle that is off by default and uses the mobile number stored on their student record. Walk-in clients may enter a number only on the PIN overlay.
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

The system further includes a real-time public queue display that shows current serving numbers and waiting queues. Student names and held tickets are not displayed publicly. A student who turns on SMS after a successful lookup may receive a GSM SMS at the mobile number stored on the student record when the ticket is about five waiting tickets away in the same fair call order used by Call Next, with an estimated wait when history is sufficient, and a second SMS when the number is called or recalled. If no mobile is on file, the SMS control stays off and disabled and shows that no SMS number is on file; print still proceeds. Walk-in clients may enter a mobile number only on the PIN overlay. Queue operations continue if the GSM module is unavailable. Queue and service activities are recorded in the local database for monitoring, history, and administrative reporting. The administrator can manage authorized users and student records, enroll RFID/NFC card serials (UID) and mobile numbers against Student IDs, set the kiosk walk-in PIN, monitor queue activities, review records, and access waiting-time and service-time reports based on completed transactions.

The system operates within the institution's Local Area Network (LAN) and does not require continuous internet connectivity for normal queue operations [22]. It uses Laravel 12, Blade, Vite/Tailwind, and MySQL/MariaDB on one mini PC that serves as both kiosk and application server (Intel Core i5 8th Generation, 8 GB RAM). The mini PC hosts the kiosk touchscreen, the 58mm XP-Q90EC printer, the ACR122U-A9 RFID/NFC reader, and the SIM7600G-H GSM/LTE module. When the printer is out of paper, the staff dashboard shows a paper-out alert.

#### 1.5.2 Delimitation

The study is limited to the selected student service offices of PECIT, specifically the Cashier, Registrar, and Data Management Office (DMO), and does not cover all institutional offices or departments.

The system is limited to local, LAN-based operation and does not include continuous cloud-based synchronization or remote internet-based administration as part of its current implementation.

The system does not include:

- online queue reservation;
- mobile application integration;
- a public cloud SMS or email gateway (notices use a local GSM/LTE module);
- cloud-based queue synchronization; and
- remote queue management through the internet.

RFID/NFC at the kiosk is used only to identify an enrolled student card at confirmation. It is not used for payment, attendance, or door access. Forgetting the physical ID is not a walk-in; the client may still type the Student ID. Students do not type a mobile number on Confirm. SMS uses the stored student mobile only when the SMS control is turned on. The kiosk does not show the full number; at most the last four digits may appear. SMS text contains the ticket number, service or window, and an estimate when available. Names are not sent by SMS.

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

The system gives the administrator centralized functions for monitoring queue activities, managing authorized users and student records, enrolling RFID/NFC card serials (UID) and mobile numbers against Student IDs, setting the kiosk walk-in PIN, reviewing transaction records, and generating reports. Recorded queue and service information can support the monitoring of waiting time, service time, completed transactions, and service-window performance. Reports are based on **done** tickets only.

#### 1.6.5 Administrative Staff

The system assists Cashier, Registrar, and DMO personnel through a structured queue process. Authorized staff can **Call Next**, **Recall**, **Hold**, **Call Held**, and **Complete**. Staff can see the student name while serving a validated student ticket, or the walk-in reason for an authorized walk-in ticket. For services with multiple windows, the system supports a shared waiting queue while keeping independent active transactions and service timers. The service-wide **2 Priority → 1 Regular** rule provides a consistent method for managing queue order.

#### 1.6.6 Students and Parents

The system provides students and parents with a more organized and transparent way of joining and monitoring queues. Through the kiosk, they can select the service, choose Regular or Priority, type or tap and validate their Student ID or complete authorized walk-in issuance, optionally turn on SMS (off by default), confirm the request, and receive a printed **number-only** ticket. Students do not type a phone number; SMS uses the number stored with the Student ID. Walk-in clients may enter a number only after the kiosk PIN is accepted. The public display allows them to monitor queue progress without remaining in a physical line. The system may show an estimated waiting time when data are sufficient, and may send **GSM SMS** notices when the ticket is near and when it is called.

#### 1.6.7 Future Researchers

The study may serve as a reference for future researchers developing queue management systems, self-service kiosks, and localized information systems for educational institutions [9]. It provides a basis for further work on additional offices, alternative scheduling, mobile or online queueing, advanced analytics, and other extensions of the current system.

### 1.7 Definition of Terms

To ensure clarity and consistency, the following terms are defined conceptually and operationally based on their use in the study and the developed system.

**Administrative Staff** – Refers to authorized personnel assigned to the Cashier, Registrar, and Data Management Office (DMO) who process student transactions and use the staff interface to manage queues.

**Call Held** – Refers to the staff function used to resume a previously held queue ticket at a service window. A held ticket can be resumed without following the normal **2 Priority → 1 Regular** calling sequence.

**Cashier** – Refers to the PECIT student service office responsible for processing payment-related and other financial transactions.

**Data Management Office (DMO)** – Refers to the PECIT office responsible for handling student data-related services, including record verification and other related transactions.

**Estimated Waiting Time** – Refers to the calculated approximate time a client may wait before being served, based on available current queue information and sufficient historical service data.

**GSM SMS Notice** – Refers to an optional **number-only** text sent through a SIM7600G-H GSM/LTE USB module on the mini PC that also hosts the kiosk and application server. A student turns on **Notify me by SMS** at confirmation (off by default). The notice uses the mobile number stored on the student record, not a number typed at the kiosk. If no mobile is on file, the control stays off and print still proceeds. A walk-in ticket may use a number entered only on the PIN overlay. A ticket that opted in may receive one notice when it is about five waiting tickets away in fair call order (with an estimated wait when history is sufficient) and another notice when the number is called or recalled. Queue operations do not depend on the modem.

**Interview** – Refers to the oral data-gathering method used only with Cashier, Registrar, and DMO staff during requirements analysis. Students and parents were not interviewed.

**Kiosk PIN** – Refers to the walk-in access code stored in the system settings and edited by the administrator. Every use of the kiosk walk-in control requires this PIN. The PIN is checked on the server, and the Guard role cannot log in to the system.

**Local Area Network (LAN)** – Refers to the local network used by the mini PC (kiosk and server), staff workstations, public display, administrator computer, and database to communicate within the PECIT environment without requiring continuous internet connectivity.

**Mini PC** – Refers to the Intel Core i5 8th Generation computer with 8 GB RAM that serves as both the public kiosk host and the application server.

**Multi-Counter Queue** – Refers to a service configuration in which multiple service windows share a common waiting queue while each window maintains its own active transaction and service timer. This is applied to the Cashier service in the current system.

**Notify me by SMS** – Refers to the confirmation-step control that is **off by default**. Students do not type a phone number. If a mobile number is stored with the Student ID, turning the control on copies that number to the ticket for GSM notices. The kiosk does not show the full number; at most the last four digits may appear after the control is on. If no mobile is on file, the control stays off and disabled and shows **No SMS number on file**. Confirm and print still proceed.

**Paper-Out Alert** – Refers to the notice shown on the staff dashboard when the 58mm thermal printer has no paper. Stored tickets remain valid and can be reprinted after paper is loaded.

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

**Thermal Printer** – Refers to the 58mm USB XP-58 (XP-Q90EC) printer connected to the mini PC and used to automatically produce the client's physical queue ticket after confirmation. Paper-out is reported to the staff dashboard.

**User Acceptance Testing (UAT)** – Refers to the evaluation conducted after the system was implemented, using the Cashier, Registrar, DMO, and Student System Feedback Forms, to determine whether the developed queue management system satisfies functionality, usability, efficiency, reliability and accuracy, and satisfaction requirements, using quality characteristics aligned with ISO/IEC 25010 [10]. UAT results belong in Chapter 4 and are separate from the Chapter 3 needs-assessment questionnaire.

**Walk-in Ticket** – Refers to a queue ticket issued without a Student ID after the kiosk walk-in PIN is accepted, or by an administrator. Walk-in tickets may be open at the same time, are printed as numbers only, and appear to staff as Walk-in followed by the recorded reason. The walk-in overlay may accept an optional mobile number for SMS because there is no student record.

## References

[1] Akila, I. S., Pratheek, P., & Poonia, A. (2023). Smart identity card for campus automation. In *2023 Innovations in Power and Advanced Computing Technologies (i-PACT)* (pp. 1–5). IEEE. https://doi.org/10.1109/I-PACT58649.2023.10434674

[2] Ariningsih, P., & Muhammad, A. H. (2024). Quality evaluation of ticketing management system using ISO/IEC 25010:2023 standards and AHP method. *Intechno Journal: Information Technology Journal, 6*(2). https://doi.org/10.24076/intechnojournal.2024v6i2.1870

[3] Barbado, R. M. B., Peñaflorida, L. A. E., Taclibon, W., Temprosa, H. Q., & Austria, Y. D. (2025). ADUPay: A self-service payment kiosk for Adamson University’s recognized student organization. In *2025 IEEE Malaysia International Conference on Communications (MICC)*. IEEE. https://doi.org/10.1109/MICC66164.2025.11210991

[4] Caranza, A. C. O., Cortez, C. C. G., Da, L. V., Dela Cruz, S. A. S., Rapadas, D. M., Santos, M. N. M., & Naturales, J. D. (2026). Enhancing student service efficiency through a smart enrollment queuing system for St. Clare College of Caloocan. *International Journal of Research and Innovation in Applied Science, 11*(5), 1493–1526. https://doi.org/10.51584/ijrias.2026.11050127

[5] Commission on Higher Education Regional Office XI. (2024, July 1). *CHED RO XI awarded as government champion for digital transformation at DEVCON Mindanao Summit 2024*. https://ro11.ched.gov.ph/2024/07/01/ched-ro-xi-awarded-as-government-champion-for-digital-transformation/9085/press-release/

[6] Concepcion, A. T. V., Concepcion, A. U., Guinto, C. A. A., Araza, C., Imperio, R. D. H., Regaas, B., & Perez, N. M. N. (2026). Smart queuing and appointment management system for client services in Bulacan State University Bustos Campus. *International Journal of Multidisciplinary: Applied Business and Education Research, 7*(1), 5–14. https://doi.org/10.11594/ijmaber.07.01.02

[7] Espinosa, G. S., Serrano, M. R., & Balmes, H. A. (2024). Automated enrollment queueing system in a university towards student experience and operational efficiency. *MSEUF Research Studies, 21*(1). https://www.ejournals.ph/article.php?id=24562

[8] Falcon, I. G., Dugaria, K. J. L., & Remojo, D. C. (2026). A three-tier integrated queue management system: Optimized service delivery for land transportation clients. *Journal of Engineering and Technology for Industrial Applications, 12*(57), 559–566. https://doi.org/10.5935/jetia.v12i57.2567

[9] Grepon, B. G. S., Baran, N. T., Gumonan, K. M. V. C., Martinez, A. L. M., & Lacsa, M. L. E. (2022). Designing and implementing e-school systems: An information systems approach to school management of a community college in Northern Mindanao, Philippines. *International Journal of Computing Sciences Research, 6*, 792–808. https://doi.org/10.25147/ijcsr.2017.001.1.74

[10] International Organization for Standardization. (2023). *Systems and software engineering—Systems and software Quality Requirements and Evaluation (SQuaRE)—Product quality model* (ISO/IEC 25010:2023). https://www.iso.org/standard/78176.html

[11] Linhalis, F., & Silva, A. C. (2023). Solutions to enable the use of ICT in education in regions with low internet connectivity: A literature review. *Brazilian Journal of Education, Technology and Society, 16*(4), 1185–1196. https://doi.org/10.14571/brajets.v16.n4.1185-1196

[12] Maala, R. F., Sebua, N. B., & Evangelista, K. L. L. (2023). Queuing management system in Manuel S. Enverga University Foundation Candelaria Inc. *International Journal of Advanced Research in Computer Science, 14*(6), 44–53. https://doi.org/10.26483/ijarcs.v14i6.7039

[13] Mallari, M. L. Z., Guintu, J. S., Magalong, Y. C., & Yap, D. S. (2022). CLIQUE: A web-based queue management system with real-time queue tracking and notification of units for Angeles University Foundation Office of the University Registrar. In *Proceedings of the 12th Annual International Conference on Industrial Engineering and Operations Management*. IEOM Society. https://doi.org/10.46254/an12.20220359

[14] National Privacy Commission. (2012). *Implementing rules and regulations of Republic Act No. 10173*. https://privacy.gov.ph/data-privacy-act/

[15] Panganiban, J. F., & Becenia, A. B. (2026). iSkulFlow: An event-driven queueing and transactional management system for registrar and financial services. *International Journal of Education, Research, Innovation Perspectives*. https://doi.org/10.5281/zenodo.19020670

[16] Pantonial, L., Geroy, C., & Prado, N. (2025). Student payment behaviors, queue management, and cashier efficiency in a private higher education institution. *Psychology and Education: A Multidisciplinary Journal, 36*(9), 1005–1010. https://doi.org/10.70838/pemj.360905

[17] Ramoga, M. R. (2025). Ticketless ID operated queue management optimization with SMS notification and voiceover feature. *Current Science Research Bulletin*. https://doi.org/10.5281/zenodo.16926252

[18] Republic of the Philippines. (2012). *Republic Act No. 10173: Data Privacy Act of 2012*. Official Gazette. https://www.officialgazette.gov.ph/2012/08/15/republic-act-no-10173/

[19] Saravanos, A., & Curinga, M. X. (2023). Simulating the software development lifecycle: The waterfall model. *Applied System Innovation, 6*(6), Article 108. https://doi.org/10.3390/asi6060108

[20] Sumalinog, P. S., Jr., & Mahusay, G. S. (2025). Q-Line UCLM: Enhancing student service flow through paperless. *UCLM Journal of Research and Innovation, 1*(1). https://doi.org/10.5281/zenodo.19181459

[21] Tesa, C. A., Haris, M. S., & Kusuma, W. T. (2025). An analysis of digital queuing systems in Indonesian banking services: A systematic literature review. *International Journal of Software Engineering and Computer Science, 5*(2). https://doi.org/10.35870/ijsecs.v5i2.4379

[22] UNESCO. (2023). *Global education monitoring report 2023: Technology in education: A tool on whose terms?* https://doi.org/10.54676/uzqv8501
