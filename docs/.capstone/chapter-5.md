# CHAPTER 5

## SUMMARY, CONCLUSIONS, AND RECOMMENDATIONS

This chapter summarizes the study, states the conclusions that answer the five specific objectives, and presents recommendations for PECIT, later system work, and future researchers.

### 5.1 Summary

This study designed, developed, and implemented a Standalone Kiosk-Based Queuing Management System with Thermal Printer for the Cashier, Registrar, and Data Management Office (DMO) of the Philippine Electronics and Communication Institute of Technology (PECIT). The offices previously used a manual first-come-first-served line. That process produced unclear order, line cutting, difficulty serving priority clients, crowding, and no shared view of who was waiting.

The implemented system is standalone and LAN-based [11]. One mini PC (Intel Core i5 8th Generation, 8 GB RAM) is both the public kiosk host and the application server. A student uses three kiosk steps: service, Regular or Priority, and confirmation with a validated Student ID typed on the keypad or tapped on an enrolled RFID/NFC card at the ACR122U-A9 reader. Clients who do not bring the card may still type the Student ID. Clients who cannot use a Student ID may request a walk-in ticket after the kiosk PIN stored in settings is accepted. After lookup, **Notify me by SMS** is off by default and uses the mobile number stored on the student record; students do not type a number. If no mobile is on file, the control stays off and disabled and shows **No SMS number on file**; print still proceeds. Walk-in clients may enter a number only on the PIN overlay. The system prints a 58mm number-only ticket on the XP-Q90EC printer, may send local GSM SMS through the SIM7600G-H module when an opted-in ticket is five places away and when it is called, applies 2 Priority → 1 Regular across shared windows, and shows serving and waiting numbers on a public display. When the printer is out of paper, the staff dashboard shows a paper-out alert. Staff can Call Next, Recall, Complete, Hold, and Call Held. Administrators manage users and students, enroll card serials (UID) and mobile numbers, set the walk-in PIN, may issue walk-in tickets, and generate reports from done tickets only. The Guard role cannot log in. Student names are not printed, shown on the public display, or included in SMS. The kiosk does not show the full student mobile number.

Development followed the Waterfall model: Requirements Analysis, System Design, Implementation and Development, Testing, and Deployment [19]. The four modules are the Student Kiosk Module, Staff Module, Administrator Module, and Queue Display Module.

Chapter 4 reported that the tested functions operated as intended. Cashier and Registrar scored **5.00** overall, DMO scored **4.92**, and the Student System Feedback Form scored **4.91** overall, all **Strongly Agree**. The combined user mean in Table 4.3 is **4.96**, **Strongly Agree**. The IT expert scored **5.00** overall in Table 4.4. The study does not claim a measured percentage reduction in waiting time.

### 5.2 Conclusions

Based on the development, implementation, testing, and evaluation, the researchers conclude the following:

1. A standalone kiosk-based queue process can organize Cashier, Registrar, and DMO transactions that previously depended on a manual first-come-first-served line. Shared service queues and per-window timers allow several cashiers to serve one waiting list without assigning the same ticket twice.

2. Confirmation before numbering is workable when the request is checked through Student ID validation by keypad or RFID/NFC tap, or authorized kiosk-PIN walk-in issuance. Unknown IDs, unknown cards, and a second open student ticket on the same day are rejected. Print, display, and SMS remain number-only.

3. The 58mm thermal printer gives the client a physical queue reference after confirmation. If the printer is out of paper, the staff dashboard shows a paper-out alert and the stored ticket remains valid. 2 Priority → 1 Regular calling serves priority clients fairly while multiple windows share one service queue.

4. The public display and staff panel provide a real-time view of who is being served and who is waiting. Waiting rows follow the same fair order as Call Next. Held tickets and student names are omitted from the public view. Optional local GSM SMS through the SIM7600G-H module notifies an opted-in ticket (stored student mobile, or walk-in overlay number) when the ticket is five places away in fair order and when it is called.

5. The system can be evaluated through functional testing, expert review, and User Acceptance Testing. User feedback was **Strongly Agree** (Cashier **5.00**, Registrar **5.00**, DMO **4.92**, Student form **4.91**; combined **4.96**). The IT expert scored **5.00** overall under ISO/IEC 25010-aligned characteristics [10].

The developed system is therefore a practical replacement for the manual line at the selected PECIT offices. The study does not claim a measured numerical reduction in actual waiting time because no controlled before-and-after operational measurement is reported.

### 5.3 Recommendations

The researchers recommend the following:

1. PECIT may deploy the system in the Cashier, Registrar, and DMO waiting areas using the mini PC (Intel Core i5 8th Generation, 8 GB RAM) as both kiosk and server, the 58mm XP-Q90EC printer, ACR122U-A9 RFID/NFC reader, SIM7600G-H GSM/LTE module, public display, staff PCs, and LAN already specified for standalone operation. Before go-live, network configuration should be prepared for PECIT's cloud-based network environment, as noted by the IT expert.

2. Administrators and staff should receive orientation on the three-step kiosk, Student ID and card-tap errors, walk-in PIN use, GSM SMS (**Notify me by SMS** off by default; stored student mobile; walk-in overlay number only), paper-out alerts on the staff dashboard, Call Next, Recall, Complete, Hold, and Call Held before the offices rely on the system during peak hours.

3. The mini PC, printer, RFID/NFC reader, GSM module and SIM load, display, workstations, database, and LAN should be checked regularly. Spare paper should be kept at the kiosk. The staff dashboard paper-out alert should be treated as a signal to reload the printer so stored tickets can still be reprinted.

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

These items are future work. They are not part of the implemented PECIT kiosk system. RFID/NFC tap at confirm and local GSM SMS (five-ahead and called) are part of the current build.

### 5.5 Recommendations for Future Researchers

Future researchers may evaluate the system with larger user groups and longer operational periods, and they may compare 2 Priority → 1 Regular with other call orders under measured wait and service times. Studies may also examine walk-in volume versus student-ID tickets, the effect of Hold and Call Held on window use, and whether estimated wait matches later completed times.

Researchers who add a cloud SMS API, remote reservation, or cloud monitoring should keep the privacy rule used here: Student ID is for confirmation and staff serving; names are not printed, shown on the public display, or included in SMS. Localized campus information systems remain a suitable base for that work [9].

Future researchers are encouraged to keep the standalone kiosk, thermal ticket, and fair shared-window design while collecting the operational measurements this study does not claim.
