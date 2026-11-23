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

Other Philippine systems have used a ticketless Student ID approach with SMS and voice announcement [17]. The present study takes a different route. After confirmation, the PECIT kiosk sends ticket information to a connected 58mm USB thermal printer. The printed ticket shows the queue number and may include an estimated waiting time when enough history exists. The student’s name is not printed.

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

The main difference is the queueing approach. Ramoga’s system is ticketless and uses SMS and voice announcement. The PECIT system issues a physical 58mm thermal ticket, keeps names off the ticket, public display, and SMS, and operates within a standalone LAN. SMS in the present study is optional and local (GSM/LTE module): a five-tickets-away estimate and a now-serving notice. Students opt in with **Notify me by SMS** (off by default); the notice uses the mobile number stored on the student record rather than a number typed at the kiosk. Walk-in clients who have no Student ID are handled through a kiosk PIN and may enter a mobile number only on that overlay. It is not a cloud SMS gateway and does not replace the printed ticket.

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

The proposed system therefore combines a touchscreen kiosk, Student ID validation by keypad or RFID/NFC tap, authorized kiosk-PIN walk-in issuance, Regular and Priority queueing, confirmation before ticket generation, 58mm thermal printing, estimated waiting time when data are sufficient, optional local GSM SMS after **Notify me by SMS** is turned on (stored student mobile; walk-in overlay number only), service-wide 2 Priority → 1 Regular scheduling, shared multi-counter queues, Hold and Call Held staff controls, a number-only public display, local database storage, and administrative reports based on completed transactions only.

### 2.4 Summary of the Review

The literature in this chapter supports the **research gap** already stated in **Chapter 1**. That gap is not restated here.

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
