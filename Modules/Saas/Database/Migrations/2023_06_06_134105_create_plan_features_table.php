<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      if (!Schema::hasTable('plan_features'))
        Schema::create('plan_features', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('key')->unique();
            $table->string('heading')->nullable();
            $table->string('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->integer('order')->nullable()->default(0);
            $table->foreignId('image_id')->nullable()->constrained('uploads')->cascadeOnDelete();
            $table->bigInteger('status_id')->default(1)->comment('1=active,4=inactive');
            $table->timestamps();
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $pricingPlanFeatures = [
            [
                'title' => 'HR',
                'key' => 'hr',
                'heading' => 'HR Management',
                'short_description' => 'HR Management involves overseeing personnel, from recruitment to employee development, to ensure a productive and compliant workforce.',                      
                'description' => "
                <ul>
                  <li><strong>Recruitment:</strong> The process of identifying, attracting, and selecting qualified candidates for job positions within the company. It includes creating job descriptions, conducting interviews, and evaluating applicants.</li>
                  <li><strong>Training and Development:</strong> Providing employees with the necessary skills and knowledge to excel in their roles. This can include on-the-job training, workshops, and ongoing professional development opportunities.</li>
                  <li><strong>Performance Evaluation:</strong> Assessing and measuring employee performance to identify areas for improvement and recognize outstanding contributions. Performance appraisals and feedback are commonly used for this purpose.</li>
                  <li><strong>Employee Relations:</strong> Managing relationships between employees and employers, resolving disputes, and ensuring a harmonious work environment. This includes handling grievances, conflicts, and maintaining open communication channels.</li>
                  <li><strong>Compensation and Benefits:</strong> Designing and administering compensation packages, including salaries, bonuses, and benefits such as healthcare, retirement plans, and more. Ensuring competitive and fair compensation is crucial to attracting and retaining talent.</li>
                  <li><strong>Compliance:</strong> Ensuring that the organization adheres to labor laws, regulations, and industry standards. This includes managing legal issues, workplace safety, and labor relations.</li>
                  <li><strong>HR Technology:</strong> Implementing and using technology solutions, such as HR management software, to streamline HR processes, data management, and analytics for more informed decision-making.</li>
                </ul>
                
                <p>HR Management is not only about handling administrative tasks but also strategically aligning the workforce with the organization's goals and values. It plays a vital role in creating a motivated, productive, and satisfied workforce, which in turn contributes to the overall success of the company.</p>
                ",                      
                'order' => 1,
                'image_id' => 45
            ],
            [
                'title' => 'Employees',
                'key' => 'employees',
                'heading' => 'Employee Management',
                'short_description' => 'Employee Management entails optimizing the performance, well-being, and development of staff within an organization to achieve business goals.',                        
                'description' => "
                <ul>
                  <li><strong>Individual Performance Optimization:</strong> Employee Management places a strong emphasis on enhancing the capabilities and productivity of each employee. It involves setting clear performance goals, providing feedback, and offering opportunities for skill development tailored to individual needs.</li>
                  <li><strong>Career Development:</strong> This aspect revolves around nurturing and advancing an employee's career within the organization. It includes identifying career paths, providing training, and offering growth opportunities that align with individual aspirations and company needs.</li>
                  <li><strong>Employee Engagement:</strong> Employee Management focuses on maintaining high levels of employee satisfaction and engagement. Strategies may include recognition programs, feedback mechanisms, and addressing concerns that affect individual morale.</li>
                  <li><strong>Work-Life Balance:</strong> Ensuring that employees can achieve a balance between their professional and personal lives is a unique facet of Employee Management. It may involve flexible work arrangements, time-off policies, and wellness programs that cater to individual needs.</li>
                </ul>
                
                <p>While HR Management covers a wider spectrum of HR functions, Employee Management is distinctive in its dedicated approach to enhancing the individual employee experience. It recognizes that a motivated and fulfilled workforce is the foundation of organizational success.</p>
                ",                                 
                'order' => 2,
                'image_id' => 45
            ],
            [
                'title' => 'Services',
                'key' => 'services',
                'heading' => 'Service Management',
                'short_description' => 'Administering and optimizing services to meet customer needs efficiently.',                        
                'description' => "
                <ul>
                  <li><strong>Service Design:</strong> The process of defining and designing services, considering customer requirements and business objectives. It involves creating service blueprints, identifying service components, and ensuring scalability and efficiency.</li>
                  <li><strong>Service Delivery:</strong> Implementing services to customers, including service execution, customer interaction, and service monitoring. Service level agreements (SLAs) and performance metrics are key aspects of service delivery.</li>
                  <li><strong>Service Improvement:</strong> Continuously enhancing services based on feedback, data analysis, and industry best practices. This includes identifying areas for improvement, implementing changes, and measuring their impact.</li>
                  <li><strong>Customer Relationship Management:</strong> Building and maintaining strong customer relationships, understanding their needs, and ensuring satisfaction. This often involves support services, addressing inquiries, and resolving issues in a timely manner.</li>
                  <li><strong>Service Catalog Management:</strong> Creating and managing a catalog of available services, including their descriptions, features, and associated costs, to aid customers in making informed choices.</li>
                  <li><strong>Incident and Problem Management:</strong> Responding to incidents and problems that may disrupt services, with a focus on minimizing downtime and preventing future occurrences.</li>
                  <li><strong>Service Metrics and Reporting:</strong> Measuring and reporting on service performance through key performance indicators (KPIs) to ensure that services meet defined standards and objectives.</li>
                  </ul>
                ",   
                'order' => 3,
                'image_id' => 45
            ],
            [
                'title' => 'Leaves',
                'key' => 'leaves',
                'heading' => 'Leave Management',
                'short_description' => 'Tracking and managing employee time-off requests and balances.',                        
                'description' => "
                <ul>
                  <li><strong>Leave Types:</strong> Defining and categorizing the various types of leave available to employees, such as vacation, sick leave, personal days, and more.</li>
                  <li><strong>Leave Policies:</strong> Establishing clear and transparent policies outlining how leave requests are submitted, approved, and managed. This includes rules for accrual, carry-over, and limitations on leaves.</li>
                  <li><strong>Leave Requests:</strong> Providing employees with a mechanism to request time off, typically through a leave management system or HR portal. Requests may require specific information, including the dates, reason, and duration of the leave.</li>
                  <li><strong>Leave Approval:</strong> Designating a process for supervisors or managers to review and approve leave requests based on organizational policies, workloads, and staffing requirements.</li>
                  <li><strong>Leave Balances:</strong> Maintaining accurate records of employees' leave balances, including accrued and used leave. These balances are often accessible to employees for transparency.</li>
                  <li><strong>Leave Tracking:</strong> Monitoring employee absences and ensuring that they align with approved leave requests. This includes managing unplanned or unexpected leaves, such as sick days or family emergencies.</li>
                  <li><strong>Compliance and Legal Aspects:</strong> Adhering to labor laws and regulations related to leave entitlements, such as family and medical leave, maternity or paternity leave, and paid time off (PTO).</li>
                  <li><strong>Reporting and Analytics:</strong> Utilizing leave data for reporting and analysis to make informed decisions, optimize workforce planning, and ensure business continuity.</li>
                </ul>
                
                <p>Leave Management is vital for maintaining a well-balanced work environment, ensuring that employees can take the necessary time off while also meeting the organization's operational needs. Effective Leave Management fosters employee satisfaction, reduces absenteeism, and promotes a healthy work-life balance.</p>
                ",   
                'order' => 4,
                'image_id' => 45
            ],
            [
                'title' => 'Attendance',
                'key' => 'attendance',
                'heading' => 'Attendance Management',
                'short_description' => 'Monitoring and maintaining records of employee work hours.',                        
                'description' => "
                <ul>
                  <li><strong>Time Tracking:</strong> Recording the clock-in and clock-out times of employees, either manually or through automated time and attendance systems, to create an accurate record of working hours.</li>
                  <li><strong>Attendance Policies:</strong> Establishing clear attendance policies that define work hours, break times, tardiness thresholds, and the consequences of non-compliance with attendance rules.</li>
                  <li><strong>Shift Scheduling:</strong> Creating and managing employee work schedules, including shift rotations, overtime scheduling, and holiday assignments to ensure adequate staffing levels.</li>
                  <li><strong>Leave and Absence Management:</strong> Integrating attendance records with leave management to track paid time off, unpaid leave, and other absence types, ensuring accurate attendance records.</li>
                  <li><strong>Overtime Management:</strong> Monitoring and controlling overtime hours to manage costs and prevent employee burnout while adhering to labor regulations governing overtime pay.</li>
                  <li><strong>Biometric or Access Control Systems:</strong> Implementing biometric or access control systems for secure and accurate employee attendance tracking, often used in conjunction with time clocks or electronic badges.</li>
                  <li><strong>Compliance:</strong> Ensuring compliance with labor laws and regulations related to work hours, breaks, and rest periods to avoid legal repercussions and fines.</li>
                  <li><strong>Reporting and Analytics:</strong> Using attendance data for reporting and analysis to gain insights into workforce performance, productivity, and to optimize scheduling and resource allocation.</li>
                </ul>
                
                <p>Attendance Management is essential for tracking employee presence, ensuring accurate payroll processing, maintaining workforce discipline, and optimizing resource allocation. It contributes to efficient workforce planning and aids in creating a productive and compliant work environment.</p>
                ",   
                'order' => 5,
                'image_id' => 45
            ],
            [
                'title' => 'Conference',
                'key' => 'conference',
                'heading' => 'Conference Management',
                'short_description' => 'Organizing and overseeing events, meetings, and conferences.',                        
                'description' => "
                <ul>
                  <li><strong>Event Planning:</strong> The process of conceptualizing, designing, and organizing conferences or events, considering goals, budgets, timelines, and target audiences.</li>
                  <li><strong>Logistics and Venue Selection:</strong> Determining the appropriate location and venue for the conference, including considerations for capacity, facilities, and accessibility.</li>
                  <li><strong>Agenda and Program Development:</strong> Creating a structured agenda and program that outlines speakers, topics, activities, and schedules for the conference.</li>
                  <li><strong>Registration and Attendee Management:</strong> Managing attendee registration, including ticket sales, communication, badges, and handling inquiries or issues related to attendance.</li>
                  <li><strong>Speaker and Presentation Management:</strong> Coordinating speakers, presentations, and audio-visual requirements, ensuring the smooth delivery of content during the event.</li>
                  <li><strong>Promotion and Marketing:</strong> Developing and executing marketing strategies to attract participants and sponsors to the conference, including online and offline promotion efforts.</li>
                  <li><strong>Budget and Financial Management:</strong> Managing the budget, financial transactions, and expenses related to the conference, including sponsorships, ticket sales, and vendor contracts.</li>
                  <li><strong>Logistics and On-Site Operations:</strong> Overseeing the logistical aspects of the conference, such as registration desks, technical setups, and handling unexpected issues during the event.</li>
                  <li><strong>Post-Conference Evaluation:</strong> Collecting feedback, analyzing the success of the conference, and identifying areas for improvement for future events.</li>
                </ul>
                
                <p>Conference Management is crucial for ensuring that conferences and events are executed flawlessly, providing value to participants, sponsors, and organizers. It requires careful planning, strong coordination, and attention to detail to create a memorable and productive conference experience.</p>
                ",   
                'order' => 6,
                'image_id' => 45
            ],
            [
                'title' => 'Payroll',
                'key' => 'payroll',
                'heading' => 'Payroll Management',
                'short_description' => 'Calculating and processing employee salaries and benefits.',                        
                'description' => "
                <ul>
                  <li><strong>Salary Calculation:</strong> Determining each employee's gross pay, which includes their regular salary, bonuses, overtime, and other earnings, as well as any deductions or withholdings.</li>
                  <li><strong>Taxation and Withholding:</strong> Ensuring that federal, state, and local taxes are correctly withheld from employees' paychecks and remitted to the appropriate tax authorities.</li>
                  <li><strong>Benefits Administration:</strong> Managing employee benefits, including health insurance, retirement plans, and other perks, and ensuring that deductions and contributions are correctly accounted for.</li>
                  <li><strong>Compliance:</strong> Adhering to labor laws, tax regulations, and government reporting requirements, including timely filings of payroll-related taxes and reports such as W-2 forms.</li>
                  <li><strong>Payment Processing:</strong> Executing payments to employees through various methods, such as direct deposit or printed checks, in accordance with pay schedules and company policies.</li>
                  <li><strong>Record-Keeping:</strong> Maintaining precise records of payroll transactions, employee earnings, deductions, and compliance-related documents for auditing and reporting purposes.</li>
                  <li><strong>Garnishments and Deductions:</strong> Managing court-ordered garnishments, child support orders, and other legal deductions from employee wages as required by law.</li>
                  <li><strong>Salary Structure and Compensation Planning:</strong> Developing and maintaining a structured salary framework and compensation strategies to attract and retain top talent within budgetary constraints.</li>
                  <li><strong>Payroll Software and Technology:</strong> Utilizing payroll management software and technology to streamline processes, automate calculations, and ensure accuracy in payroll processing.</li>
                </ul>
                
                <p>Effective Payroll Management is essential for maintaining employee satisfaction, adhering to legal requirements, and accurately reflecting the financial health of an organization. It requires precision, compliance, and continuous updates to accommodate changing tax laws and regulations.</p>
                ",   
                'order' => 7,
                'image_id' => 45
            ],
            [
                'title' => 'Accounts',
                'key' => 'accounts',
                'heading' => 'Account Management',
                'short_description' => 'Managing user accounts and permissions within a system.',                        
                'description' => "
                <ul>
                  <li><strong>User Account Creation:</strong> Creating and provisioning user accounts for employees, customers, or system users, ensuring that access is provided as needed and authorized.</li>
                  <li><strong>Access Control and Permissions:</strong> Defining and managing user permissions, specifying what actions, data, or functionalities each user is allowed to access or modify within the system or organization.</li>
                  <li><strong>User Authentication:</strong> Implementing secure authentication mechanisms, such as passwords, multi-factor authentication, or biometrics, to verify user identity and protect account access.</li>
                  <li><strong>User Profile Management:</strong> Collecting and storing user information, preferences, and personalization settings to enhance the user experience and provide personalized services or content.</li>
                  <li><strong>Password Management:</strong> Ensuring the security and recovery of passwords, allowing users to reset or change their passwords while safeguarding against unauthorized access.</li>
                  <li><strong>Account Deactivation and Deletion:</strong> Managing the process of deactivating or deleting accounts when users leave an organization or when no longer needed, with proper data retention and compliance measures.</li>
                  <li><strong>Account Auditing and Logging:</strong> Maintaining logs and records of account-related activities, including account creation, changes, and access attempts for security and compliance purposes.</li>
                  <li><strong>User Role Management:</strong> Defining roles and groups to simplify the assignment of permissions to multiple users, ensuring consistency and efficiency in account management.</li>
                  <li><strong>Account Security and Compliance:</strong> Ensuring that account management practices align with security policies, regulations, and best practices to protect sensitive data and maintain compliance with relevant standards.</li>
                </ul>
                
                <p>Account Management is integral to maintaining a secure, efficient, and organized user environment. Whether for employees, customers, or system users, effective account management ensures that users have the right access and permissions, enhancing their experience and safeguarding sensitive information.</p>
                ",   
                'order' => 8,
                'image_id' => 45
            ],
            [
                'title' => 'Clients',
                'key' => 'clients',
                'heading' => 'Client Management',
                'short_description' => 'Handling relationships and interactions with clients or customers.',                        
                'description' => "
                <ul>
                  <li><strong>Client Onboarding:</strong> Welcoming new clients and guiding them through the initial steps of establishing a working relationship, which may include paperwork, contracts, and introductions to key contacts.</li>
                  <li><strong>Client Relationship Development:</strong> Nurturing and maintaining ongoing relationships with clients, understanding their needs, preferences, and objectives, and providing solutions or services to meet them.</li>
                  <li><strong>Communication and Engagement:</strong> Establishing effective channels of communication and engagement to keep clients informed, address inquiries, and gather feedback to enhance services or products.</li>
                  <li><strong>Client Support and Issue Resolution:</strong> Providing assistance to clients, addressing concerns, resolving issues, and ensuring a high level of satisfaction with the products or services offered.</li>
                  <li><strong>Account Management:</strong> Managing client accounts, including billing, invoicing, and account maintenance, to ensure transparency, accuracy, and efficiency in financial transactions.</li>
                  <li><strong>Sales and Upselling:</strong> Identifying opportunities for cross-selling or upselling products or services that align with the client's needs, potentially increasing revenue and value for both parties.</li>
                  <li><strong>Client Feedback and Improvement:</strong> Soliciting and analyzing client feedback to identify areas for improvement, adapting strategies, products, or services to meet changing client expectations.</li>
                  <li><strong>Client Retention and Loyalty:</strong> Implementing strategies to foster client loyalty, including loyalty programs, incentives, and retention efforts to minimize client turnover.</li>
                  <li><strong>Data and Relationship Management:</strong> Maintaining accurate records of client information, interactions, and preferences to provide personalized experiences and to inform business decisions.</li>
                </ul>
                
                <p>Client Management is fundamental for businesses to build trust, foster loyalty, and ensure the long-term success of client relationships. By effectively managing client interactions and addressing their needs, businesses can drive growth and create lasting partnerships.</p>
                ",   
                'order' => 9,
                'image_id' => 45
            ],
            [
                'title' => 'Tasks',
                'key' => 'tasks',
                'heading' => 'Task Management',
                'short_description' => 'Planning, tracking, and organizing tasks and assignments.',                        
                'description' => "
                <ul>
                  <li><strong>Task Creation and Assignment:</strong> Creating tasks, specifying their details, and assigning them to individuals or teams responsible for their completion.</li>
                  <li><strong>Priority and Deadline Setting:</strong> Assigning priorities to tasks and defining deadlines to ensure that tasks are completed in a timely manner and in order of importance.</li>
                  <li><strong>Task Progress Tracking:</strong> Monitoring the progress of tasks, tracking their status, and identifying bottlenecks or issues that may hinder completion. This often involves the use of task management tools or software.</li>
                  <li><strong>Task Collaboration:</strong> Facilitating collaboration among team members by allowing them to share information, files, and updates related to tasks, ensuring transparency and teamwork.</li>
                  <li><strong>Task Dependencies:</strong> Identifying task dependencies and relationships, ensuring that tasks are sequenced correctly and that the completion of one task triggers the start of another.</li>
                  <li><strong>Task Reminders and Notifications:</strong> Sending reminders and notifications to task assignees to keep them informed about upcoming deadlines or changes in task status.</li>
                  <li><strong>Task Documentation:</strong> Creating and maintaining documentation related to tasks, including task descriptions, checklists, and associated files or resources.</li>
                  <li><strong>Task Reporting and Analysis:</strong> Generating reports and conducting analysis to evaluate task performance, team productivity, and areas for improvement in task management processes.</li>
                  <li><strong>Task Automation:</strong> Automating routine or repetitive tasks to increase efficiency, reduce manual work, and ensure consistency in task execution.</li>
                </ul>
                
                <p>Effective Task Management is crucial for meeting project goals, improving productivity, and ensuring that tasks are executed in an organized and efficient manner. Task management tools and methodologies help teams and organizations manage their workloads and priorities effectively.</p>
                ",   
                'order' => 10,
                'image_id' => 45
            ],
            [
                'title' => 'Projects',
                'key' => 'projects',
                'heading' => 'Project Management',
                'short_description' => 'Coordinating tasks, resources, and timelines for successful project delivery.',                        
                'description' => "
                <ul>
                  <li><strong>Project Planning:</strong> Defining project objectives, scope, deliverables, and creating a comprehensive project plan that outlines tasks, timelines, and resource requirements.</li>
                  <li><strong>Task Assignment and Management:</strong> Assigning responsibilities to team members, tracking task progress, and ensuring that project activities are completed on time and within budget.</li>
                  <li><strong>Resource Allocation:</strong> Allocating and managing resources such as personnel, materials, and equipment to meet project requirements and achieve project goals efficiently.</li>
                  <li><strong>Risk Assessment and Mitigation:</strong> Identifying potential risks and issues that may affect the project and developing strategies to mitigate or manage them to minimize disruptions or delays.</li>
                  <li><strong>Project Scheduling:</strong> Creating project schedules that outline task sequences, dependencies, and timelines to ensure a logical flow of work throughout the project's lifecycle.</li>
                  <li><strong>Budget and Cost Management:</strong> Developing and managing project budgets, monitoring costs, and ensuring that expenses remain within the approved budget limits throughout the project's duration.</li>
                  <li><strong>Communication and Stakeholder Management:</strong> Establishing clear communication channels, reporting structures, and managing relationships with stakeholders to keep them informed and engaged throughout the project's lifecycle.</li>
                  <li><strong>Quality Assurance and Control:</strong> Implementing processes to ensure that project deliverables meet established quality standards and conducting inspections or testing to validate compliance.</li>
                  <li><strong>Change Management:</strong> Managing changes to project scope, objectives, or requirements while maintaining control over project timelines and budgets, preventing scope creep.</li>
                  <li><strong>Project Documentation:</strong> Creating and maintaining project documentation, including project plans, status reports, meeting minutes, and project-related files for future reference and audits.</li>
                </ul>
                
                <p>Project Management is instrumental in delivering projects on time, within budget, and with high-quality results. It ensures that project objectives are met and that all project stakeholders are satisfied with the outcomes. Effective Project Management often involves the use of methodologies and tools to streamline project processes and enhance collaboration among project team members.</p>
                ",   
                'order' => 11,
                'image_id' => 45
            ],
            [
                'title' => 'Awards',
                'key' => 'awards',
                'heading' => 'Award Management',
                'short_description' => 'Administering recognition and awards programs for employees.',                        
                'description' => "
                <ul>
                  <li><strong>Award Program Design:</strong> Defining the objectives, criteria, and types of awards to be offered, including recognition for outstanding performance, achievements, or contributions.</li>
                  <li><strong>Nomination and Selection:</strong> Establishing processes for nominating individuals or teams for awards and selecting recipients based on the program's criteria, often involving committees or panels for evaluation.</li>
                  <li><strong>Recognition and Presentation:</strong> Planning and executing award ceremonies or recognition events to honor recipients and publicly acknowledge their accomplishments or contributions.</li>
                  <li><strong>Communication and Promotion:</strong> Promoting the award program to encourage participation, educating stakeholders on its significance, and generating excitement around recognition opportunities.</li>
                  <li><strong>Award Tracking and Management:</strong> Maintaining records of award recipients, managing the logistics of awards, and ensuring that recipients receive their awards in a timely and organized manner.</li>
                  <li><strong>Feedback and Evaluation:</strong> Gathering feedback from recipients and stakeholders to evaluate the effectiveness of the award program and identify areas for improvement or refinement.</li>
                  <li><strong>Budget and Resources:</strong> Allocating resources and managing the budget for award programs, including the costs of awards, events, and promotional materials.</li>
                  <li><strong>Compliance and Fairness:</strong> Ensuring that award programs comply with relevant policies, regulations, and ethical standards while promoting fairness and transparency in the selection process.</li>
                  <li><strong>Award Catalog and Offerings:</strong> Developing and maintaining a catalog of available awards, which may include certificates, trophies, monetary prizes, or other forms of recognition. This catalog can evolve to align with changing preferences and achievements.
                </ul>
                
                <p>Award Management is a vital component of organizational culture, fostering motivation, engagement, and a sense of achievement among employees or stakeholders. It celebrates excellence and contributions, driving performance and loyalty within the organization.</p>
                ",  
                'order' => 12,
                'image_id' => 45
            ],
            [
                'title' => 'Travels',
                'key' => 'travels',
                'heading' => 'Travel Management',
                'short_description' => 'Organizing and monitoring business-related travel arrangements.',                        
                'description' => "
                <ul>
                  <li><strong>Travel Policy Development:</strong> Creating and enforcing a comprehensive travel policy that defines guidelines, approval processes, and reimbursement procedures for business travel.</li>
                  <li><strong>Travel Booking and Reservations:</strong> Arranging transportation, accommodation, and other travel-related services for employees, often using online booking systems or travel agencies to secure the best options.</li>
                  <li><strong>Expense Management:</strong> Managing travel expenses, including receipts, reimbursements, and compliance with the travel policy and financial guidelines. This may involve the use of expense management software.</li>
                  <li><strong>Travel Approval and Authorization:</strong> Implementing procedures for obtaining approval for travel plans, ensuring alignment with business goals, and verifying the necessity and cost-effectiveness of the trips.</li>
                  <li><strong>Travel Safety and Security:</strong> Addressing travel safety concerns, such as providing employees with information on security risks, offering assistance in case of emergencies, and tracking employee locations during travel, if necessary.</li>
                  <li><strong>Travel Itinerary and Document Management:</strong> Maintaining and distributing detailed itineraries, travel documents, and reservations to employees, making travel as smooth and hassle-free as possible.</li>
                  <li><strong>Vendor and Supplier Management:</strong> Establishing relationships with travel vendors, negotiating contracts, and managing service providers to secure competitive rates and consistent service quality.</li>
                  <li><strong>Travel Reporting and Analysis:</strong> Collecting data on travel expenses, trends, and patterns to optimize travel budgets, evaluate the cost-effectiveness of travel, and identify opportunities for savings or efficiencies.</li>
                  <li><strong>Travel Technology Integration:</strong> Leveraging travel management software and technology tools to streamline the booking process, monitor expenses, and enhance overall travel management efficiency.
                </ul>
                
                <p>Travel Management plays a pivotal role in ensuring that business travel is organized, efficient, and cost-effective. It contributes to the safety and well-being of employees while aligning travel activities with the organization's goals and policies.</p>
                ",  
                'order' => 13,
                'image_id' => 45
            ],
            [
                'title' => 'Performance',
                'key' => 'performance',
                'heading' => 'Performance Management',
                'short_description' => 'Evaluating and enhancing employee performance and goals.',                        
                'description' => "
                <ul>
                  <li><strong>Goal Setting:</strong> Defining clear, specific, and measurable performance goals and objectives for individuals, teams, and the organization as a whole.</li>
                  <li><strong>Performance Appraisal:</strong> Conducting regular assessments of employee performance, often through formal reviews or evaluations, to provide feedback and align performance with expectations.</li>
                  <li><strong>Feedback and Coaching:</strong> Offering ongoing feedback, coaching, and development opportunities to help employees improve their performance and reach their potential.</li>
                  <li><strong>Development and Training:</strong> Providing training, resources, and development programs to enhance employee skills and knowledge and address performance gaps.</li>
                  <li><strong>Recognition and Rewards:</strong> Acknowledging and rewarding exceptional performance through recognition programs, incentives, promotions, or bonuses to motivate and retain high-performing employees.</li>
                  <li><strong>Performance Metrics and KPIs:</strong> Using key performance indicators (KPIs) and performance metrics to measure, track, and analyze individual and team performance against established benchmarks.</li>
                  <li><strong>Performance Improvement Plans:</strong> Creating action plans to address subpar performance, outlining steps for improvement, and providing support and resources for underperforming employees.</li>
                  <li><strong>Career and Succession Planning:</strong> Identifying opportunities for career growth and succession planning, aligning employee skills and aspirations with organizational needs.</li>
                  <li><strong>Performance Documentation:</strong> Maintaining accurate records of performance-related discussions, evaluations, and improvement plans for reference and future decisions.
                  </li>
                  <li><strong>Performance Management Software:</strong> Utilizing performance management software and tools to streamline the performance appraisal process, monitor progress, and facilitate continuous feedback.
                </ul>
                
                <p>Performance Management is a critical function that aligns individual and team performance with organizational goals. It promotes continuous improvement, employee development, and a culture of excellence within the organization, ultimately driving success and competitiveness.</p>
                ",  
                'order' => 14,
                'image_id' => 45
            ],
            [
                'title' => 'Meeting',
                'key' => 'meeting',
                'heading' => 'Meeting Management',
                'short_description' => 'Efficiently planning and overseeing various types of meetings.',                        
                'description' => "
                <ul>
                  <li><strong>Meeting Planning:</strong> Defining the purpose and objectives of a meeting, determining the participants, and setting an agenda to guide the discussion.</li>
                  <li><strong>Meeting Scheduling:</strong> Arranging a suitable date, time, and location for the meeting, considering the availability and preferences of participants and ensuring logistical arrangements.</li>
                  <li><strong>Invitations and Reminders:</strong> Sending invitations, notifications, and reminders to participants, along with any relevant documents, materials, or pre-reading necessary for the meeting's effectiveness.</li>
                  <li><strong>Meeting Facilitation:</strong> Leading or facilitating the meeting to ensure that it stays on track, participants adhere to the agenda, and the meeting objectives are met efficiently.
                  </li>
                  <li><strong>Documentation and Minutes:</strong> Recording and documenting meeting proceedings, including decisions, action items, and discussions, in meeting minutes, and distributing them to participants afterward.</li>
                  <li><strong>Follow-up and Action Items:</strong> Ensuring that action items or tasks assigned during the meeting are tracked, completed, and reported on in subsequent meetings or updates.
                  </li>
                  <li><strong>Technology and Tools:</strong> Leveraging meeting management software and technology, such as video conferencing platforms, to facilitate virtual meetings, share documents, and enhance collaboration.
                  </li>
                  <li><strong>Evaluation and Feedback:</strong> Collecting feedback from meeting participants to assess the effectiveness of the meeting and identify areas for improvement.
                  </li>
                  <li><strong>Cost Management:</strong> Monitoring and controlling the costs associated with meetings, including expenses for venue rentals, catering, technology, and materials.
                  </li>
                </ul>
                
                <p>Effective Meeting Management ensures that meetings are purposeful, productive, and contribute to the organization's goals. It minimizes wasted time and resources while promoting clear communication, decision-making, and accountability among participants.</p>
                ",  
                'order' => 15,
                'image_id' => 45
            ],
            [
                'title' => 'Appointment',
                'key' => 'appointment',
                'heading' => 'Appointment Management',
                'short_description' => 'Scheduling and managing appointments and bookings.',                        
                'description' => "
                <ul>
                  <li><strong>Appointment Scheduling:</strong> Offering a straightforward system for clients or customers to book appointments with businesses, professionals, or service providers, often through online scheduling tools or phone reservations.</li>
                  <li><strong>Availability and Resource Management:</strong> Managing the availability of resources, such as employees, equipment, or facilities, to ensure that appointments can be accommodated without overbooking or causing scheduling conflicts.</li>
                  <li><strong>Appointment Confirmation:</strong> Sending confirmations and reminders to clients or customers via email, SMS, or other means to reduce no-shows and missed appointments.</li>
                  <li><strong>Rescheduling and Cancellation Policies:</strong> Defining clear policies for rescheduling or canceling appointments and communicating these policies to clients to minimize disruptions and revenue loss.</li>
                  <li><strong>Waitlist Management:</strong> Managing waitlists for appointments in case of cancellations or last-minute scheduling opportunities, ensuring efficient use of resources.
                  </li>
                  <li><strong>Appointment Reminders:</strong> Sending pre-appointment reminders to clients to ensure they are prepared for their scheduled appointments and have any required documentation or information.
                  </li>
                  <li><strong>Document Management:</strong> Maintaining appointment-related documents, such as forms, contracts, or records, and ensuring they are readily available and properly organized.
                  </li>
                  <li><strong>Payment and Invoicing:</strong> Handling payments and invoicing related to appointments, collecting fees, and issuing invoices as applicable for services rendered.
                  </li>
                  <li><strong>Reporting and Analytics:</strong> Collecting data on appointment metrics, such as appointment volumes, revenue, and customer satisfaction, to optimize appointment management processes.
                  </li>
                  <li><strong>Technology Integration:</strong> Leveraging appointment scheduling software and online tools to streamline the booking process, enhance communication, and provide convenience for clients.
                  </li>
                </ul>
                
                <p>Appointment Management is crucial for businesses and service providers to efficiently manage their schedules, optimize resource utilization, and provide a convenient and organized experience for clients or customers. It reduces no-shows, enhances customer satisfaction, and maximizes revenue potential.</p>
                ",  
                'order' => 16,
                'image_id' => 45
            ],
            [
                'title' => 'Visit', 
                'key' => 'visit',
                'heading' => 'Visit Management',
                'short_description' => 'Handling visitor registration and tracking at a location.',                        
                'description' => "
                <ul>
                  <li><strong>Visitor Registration:</strong> Providing a user-friendly registration process for visitors to enter their information, such as name, contact details, purpose of the visit, and any required documentation.</li>
                  <li><strong>Check-In and Check-Out:</strong> Managing the arrival and departure of visitors, ensuring a smooth check-in process and verifying their identities, if necessary.</li>
                  <li><strong>Access Control:</strong> Implementing access control systems, such as visitor badges, electronic key cards, or security measures, to ensure that visitors have access to authorized areas only.</li>
                  <li><strong>Visitor Identification:</strong> Verifying the identity of visitors, particularly in secure or regulated environments, and issuing identification badges or passes as needed.
                  </li>
                  <li><strong>Security and Compliance:</strong> Ensuring compliance with security and safety regulations, visitor policies, and maintaining records for auditing purposes.
                  </li>
                  <li><strong>Appointment Scheduling:</strong> Coordinating visits with appointments, ensuring that visitors are expected and received by the appropriate hosts or contact persons.
                  </li>
                  <li><strong>Visitor Records:</strong> Maintaining accurate records of visitor information, including visit dates, entry and exit times, and the purpose of visits.
                  </li>
                  <li><strong>Notification and Alerts:</strong> Sending notifications to hosts or responsible parties when their scheduled visitors arrive or need assistance.
                  </li>
                  <li><strong>Visitor Experience:</strong> Ensuring a positive visitor experience by providing clear directions, assistance, and any necessary information to enhance their visit.
                  </li>
                  <li><strong>Reporting and Analytics:</strong> Collecting data on visitor metrics, such as visitor volumes, purpose of visits, and wait times, to optimize the visit management process.
                  </li>
                </ul>
                
                <p>Visit Management is crucial for organizations that receive visitors, clients, or guests regularly. It helps maintain a secure and organized environment while providing a welcoming and efficient experience for visitors and enhancing overall security and compliance.</p>
                ",  
                'order' => 17,
                'image_id' => 45
            ],
            [
                'title' => 'Support',
                'key' => 'support',
                'heading' => 'Support Management',
                'short_description' => 'Providing assistance and resolving issues for customers or users.',                        
                'description' => "
                <ul>
                  <li><strong>Customer Support Channels:</strong> Establishing various support channels, such as phone, email, live chat, or helpdesk systems, to enable customers to seek assistance in their preferred manner.</li>
                  <li><strong>Support Ticketing:</strong> Managing support requests and issues through a ticketing system, assigning, tracking, and prioritizing them for timely resolution.
                  </li>
                  <li><strong>Knowledge Base and Self-Help:</strong> Providing a comprehensive knowledge base or self-help resources for customers to find answers to common queries or resolve issues independently.
                  </li>
                  <li><strong>Service Level Agreements (SLAs):</strong> Defining SLAs that specify response times, resolution times, and other service commitments to ensure timely support and meet customer expectations.
                  </li>
                  <li><strong>Issue Triage and Escalation:</strong> Categorizing and prioritizing support issues, and escalating them to higher-level support or specialized teams when necessary.
                  </li>
                  <li><strong>Agent Training and Development:</strong> Training support agents to have the necessary skills and knowledge to address customer issues effectively and providing ongoing development opportunities.
                  </li>
                  <li><strong>Performance Metrics and Reporting:</strong> Collecting data on support metrics, such as response times, resolution rates, customer satisfaction, and ticket backlog, for analysis and continuous improvement.
                  </li>
                  <li><strong>Customer Feedback and Surveys:</strong> Gathering feedback from customers to evaluate the quality of support services and identify areas for improvement.
                  </li>
                  <li><strong>Automation and Chatbots:</strong> Implementing automation and chatbots to handle routine support queries, free up support agents' time, and provide quick responses to customers.
                  </li>
                  <li><strong>Compliance and Quality Assurance:</strong> Ensuring that support services comply with relevant regulations and industry standards while maintaining quality in service delivery.
                  </li>
                </ul>
                
                <p>Support Management is essential for providing excellent customer service, addressing issues promptly, and maintaining customer satisfaction. It helps organizations build and maintain positive relationships with their customers and users while ensuring effective issue resolution.</p>
                ",  
                'order' => 18,
                'image_id' => 45
            ],
            [
                'title' => 'Announcement',
                'key' => 'announcement',
                'heading' => 'Announcement Management',
                'short_description' => 'Disseminating important information and announcements.',                        
                'description' => "
                <ul>
                  <li><strong>Announcement Planning:</strong> Strategically planning announcements, including defining the purpose, content, and target audience for each announcement.</li>
                  <li><strong>Content Creation:</strong> Developing announcement content, such as written messages, visuals, or multimedia, that effectively conveys the intended information or message.
                  </li>
                  <li><strong>Channel Selection:</strong> Choosing appropriate communication channels for announcements, including email, intranet, social media, bulletin boards, or other internal and external platforms.
                  </li>
                  <li><strong>Scheduling and Timing:</strong> Determining the timing of announcements to ensure they reach the intended audience at the right moment and are not disruptive.
                  </li>
                  <li><strong>Approval and Review:</strong> Implementing a review and approval process to ensure the accuracy, consistency, and relevance of announcements before they are shared.
                  </li>
                  <li><strong>Feedback and Interaction:</strong> Encouraging feedback, questions, or comments from the audience in response to announcements and providing mechanisms for two-way communication.
                  </li>
                  <li><strong>Crisis and Emergency Communications:</strong> Managing crisis or emergency announcements, including protocols for rapid dissemination of critical information.
                  </li>
                  <li><strong>Tracking and Reporting:</strong> Monitoring the performance of announcements, including metrics such as reach, engagement, and effectiveness, and using data for continuous improvement.
                  </li>
                  <li><strong>Archiving and Documentation:</strong> Maintaining a record of past announcements and related documentation for reference and compliance purposes.
                  </li>
                  <li><strong>Compliance and Regulations:</strong> Ensuring that announcements comply with relevant regulations, policies, and industry standards.
                  </li>
                </ul>
                
                <p>Announcement Management is crucial for keeping employees or stakeholders informed, engaged, and aligned with organizational goals. Effective management ensures that announcements are clear, well-timed, and contribute to a positive and informed work environment.</p>
                ",  
                'order' => 19,
                'image_id' => 45
            ],
            [
                'title' => 'Contacts',
                'key' => 'contacts',
                'heading' => 'Contact Management',
                'short_description' => 'Organizing and maintaining contact details and interactions.',                        
                'description' => "
                <ul>
                  <li><strong>Contact Database:</strong> Creating and maintaining a centralized database or system for storing contact information, including names, phone numbers, email addresses, and additional details.
                  </li>
                  <li><strong>Contact Segmentation:</strong> Categorizing contacts into groups or segments based on criteria such as demographics, preferences, or interactions for targeted communication.
                  </li>
                  <li><strong>Contact Interaction Tracking:</strong> Recording and tracking interactions with contacts, including emails, phone calls, meetings, purchases, or support requests.
                  </li>
                  <li><strong>Contact Communication:</strong> Facilitating communication with contacts through various channels, such as email, SMS, phone calls, or social media, and ensuring that messages are relevant and personalized.
                  </li>
                  <li><strong>Contact Import and Export:</strong> Enabling the import of contact lists from various sources and allowing the export of contact data for use in marketing or communication campaigns.
                  </li>
                  <li><strong>Contact Relationship Management (CRM):</strong> Utilizing CRM software or tools to manage and nurture relationships with contacts, including tracking sales leads, opportunities, and customer journeys.
                  </li>
                  <li><strong>Privacy and Data Protection:</strong> Ensuring compliance with data protection regulations and privacy standards when collecting, storing, and using contact information.
                  </li>
                  <li><strong>Contact Insights and Analytics:</strong> Analyzing contact data to gain insights into customer behavior, preferences, and engagement patterns for informed decision-making.
                  </li>
                  <li><strong>Contact History and Notes:</strong> Maintaining detailed histories and notes for each contact
                ",  
                'order' => 20,
                'image_id' => 45
            ],
            [
                'title' => 'Report',
                'key' => 'report',
                'heading' => 'Report',
                'short_description' => 'Generating, analyzing, and sharing data-driven reports.',                        
                'description' => "
                <ul>
                  <li><strong>Live Tracking</strong></li>
                  <li><strong>Location Tracking</strong></li>
                  <li><strong>Attendance Report</strong></li>
                  <li><strong>Break Report</strong></li>
                  <li><strong>Leave Report</strong></li>
                  <li><strong>Payment Report</strong></li>
                  <li><strong>Visit Report</strong></li>
                </ul>
                
                <p>Report Management is crucial for informed decision-making, performance assessment, and accountability within organizations. It provides a systematic approach to transforming data into actionable insights, enabling organizations to track progress, identify areas for improvement, and drive success.</p>
                ",  
                'order' => 21,
                'image_id' => 45
            ],
            [
                'title' => 'Configurations',
                'key' => 'configurations',
                'heading' => 'Configurations',
                'short_description' => 'Configuration involves setting up and tailoring software or systems to match specific requirements and preferences, optimizing their functionality.',                        
                'description' => "
                <ul>
                    <li><strong>Configuration</strong></li>
                    <li><strong>Weekend Setup</strong></li>
                    <li><strong>Holiday Setup</strong></li>
                    <li><strong>Shift Setup</strong></li>
                    <li><strong>Duty Schedule</strong></li>
                    <li><strong>IP Whitelist</strong></li>
                    <li><strong>Locations</strong></li>
                    <li><strong>Activation</strong></li>
                </ul>
                
                <p>Configurations are pivotal for tailoring software and systems to specific needs, optimizing performance, ensuring security, and facilitating interoperability. Effective configuration management practices are essential for maintaining the stability and reliability of complex systems and applications.</p>
                ",  
                'order' => 22,
                'image_id' => 45
              ],
            [
                'title' => 'Settings',
                'key' => 'settings',
                'heading' => 'Settings',
                'short_description' => 'Settings refer to configurable options within a system or software, allowing users to customize the behavior and appearance to suit their preferences and needs.',                        
                'description' => "
                <ul>
                    <li><strong>General Settings</strong></li>
                    <li><strong>App Setting</strong></li>
                    <li><strong>Content</strong></li>
                    <li><strong>Language</strong></li>
                </ul>
                
                <p>Settings are integral for tailoring software, applications, and devices to suit individual preferences, needs, and requirements. They empower users to create a personalized and efficient user experience, enhancing usability and functionality.</p>
                ",  
                'order' => 23,
                'image_id' => 45
            ],
            [
              'title' => 'Add-ons',
              'key' => 'add_ons',
              'heading' => 'Add-ons',
              'short_description' => '',
              'description' => "",
              'order' => 24,
              'image_id' => 45
          ],
        ];

        foreach ($pricingPlanFeatures as $feature) {
            DB::table('plan_features')->insert([
                'title'             => $feature['title'],
                'key'               => $feature['key'],
                'heading'           => $feature['heading'],
                'short_description' => $feature['short_description'],
                'description'       => $feature['description'],
                'order'             => $feature['order'],
                'image_id'          => $feature['image_id'],
                'status_id'         => 1,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plan_features');
    }
};
