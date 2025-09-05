<?php

namespace Modules\Saas\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Saas\Entities\SaasCms;

class CmsTableSeeder extends Seeder
{

    public function run()
    {
        foreach ($this->cmsData() ?? [] as $cms) {
            SaasCms::firstOrCreate([
                'title' => $cms['title'],
                'slug' => $cms['slug'],
            ], [
                'description' => $cms['description'],
                'attachment_id' => $cms['attachment_id'],
                'link' => $cms['link'],
                'text_color' => $cms['text_color'],
                'bg_color' => $cms['bg_color'],
                'order' => $cms['order'],
                'style' => $cms['style'],
                'status_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    protected function cmsData()
    {
        return [
            [
                'title' => 'Grow Your Business With ONEST HRM',
                'slug' => 'grow-your-business-with-onest-hrm',
                'description' => "Think HRM Software Is Just About Contact Management? Think Again. HubSpot HRM Has Free Tools For Everyone On Your Team, It's 100% Free",
                'attachment_id' => 49,
                'link' => 'https://www.youtube.com/watch?v=Gusi6D71Cnc',
                'text_color' => '#2C2C51',
                'bg_color' => '#f6f6f6',
                'order' => 0,
                'style' => 6,
            ],
            [
                'title' => 'HRM with SaaS Features',
                'slug' => 'hrm-with-saas-features',
                'description' => 'Thank you for choosing our powerful Software as a Service (SaaS) platform. We are excited to have you on board and ready to help you streamline your operations, boost productivity, and achieve your business goals. Whether you are a small business owner, a member of a growing team, or part of a large enterprise, our SaaS platform is designed to meet your unique needs. Thank you for choosing our SaaS platform. We look forward to helping your business thrive in the digital age. If you ever have questions or need assistance, don\'t hesitate to contact our support team for help.',
                'attachment_id' => 41,
                'link' => env('APP_URL') . '/pages/content/hrm-with-saas-features',
                'text_color' => '#ffffff',
                'bg_color' => '#4F46E5',
                'order' => 1,
                'style' => 1,
            ],
            [
                'title' => 'Powerful SaaS Dashboard Panel',
                'slug' => 'powerful-saas-dashboard-panel',
                'description' => '
                    <p>Salesforce is a leading customer relationship management (CRM) platform. It empowers businesses to manage their sales, customer support, marketing, and analytics all in one integrated solution. With its cloud-based approach, it allows for easy customization and scalability.</p>
    
                    <p>Microsoft 365, formerly known as Office 365, is a comprehensive suite of productivity tools. It includes applications like Word, Excel, and PowerPoint, as well as cloud-based collaboration and communication tools like Microsoft Teams and SharePoint.</p>
    
                    <p>AWS is the worlds most comprehensive and widely adopted cloud platform. It provides a vast array of SaaS offerings, including artificial intelligence, machine learning, analytics, and more. AWS empowers businesses to innovate and scale with unparalleled flexibility.</p>
    
                    <p>Slack is a communication and collaboration platform that enhances team productivity. It offers chat, file sharing, integrations, and customizable workflows to streamline internal communication.</p>
    
                ',
                'attachment_id' => 42,
                'link' => env('APP_URL') . '/pages/content/powerful-saas-dashboard-panel',
                'text_color' => '#000000',
                'bg_color' => '#F1EAFF',
                'order' => 2,
                'style' => 2
            ],
            [
                'title' => 'Customer Success Stories',
                'slug' => 'customer-success-stories',
                'description' => '
    
                <p>FoxNet, a small e-commerce business, leveraged our SaaS solutions to streamline their operations and improve customer engagement. Within a year of using our platform, they achieved a 150% increase in sales, thanks to better data analytics and marketing tools.</p>
    
                <p>Alpin Soft, a medium-sized enterprise, adopted our SaaS solutions to enhance internal communication and project management. They saw a 30% reduction in project turnaround time and improved collaboration, leading to higher client satisfaction and repeat business.</p>
    
                <p>TechLite, a tech startup, utilized Amazon Web Services (AWS) to scale their infrastructure seamlessly. This allowed them to handle a sudden surge in user traffic without downtime, demonstrating the flexibility and reliability of AWS\'s cloud services.</p>
    
                <p>ShipTech, a design agency, integrated Adobe Creative Cloud into their workflow, resulting in a 40% increase in productivity. They now deliver high-quality designs and content to their clients faster and more efficiently.</p>
    
                <p>ToLets, a global team, utilized Dropbox to securely store and share project files across borders. With Dropbox\'s file management capabilities, they improved collaboration and data accessibility while maintaining top-notch security.</p>
    
                ',
                'attachment_id' => 43,
                'link' => env('APP_URL') . '/pages/content/customer-success-stories',
                'text_color' => '#000000',
                'bg_color' => '#FFFBF5',
                'order' => 3,
                'style' => 3
            ],
            [
                'title' => 'Get Started with Our SaaS Mobile Application',
                'slug' => 'get-started-with-out-saas-mobile-application',
                'description' => '
                <p>Ready to get started with our powerful SaaS platform? Follow these simple steps to begin your journey with us:</p>
                <p>Start by creating an account. Click the "Sign Up" button and provide the necessary information. It\'s quick, easy, and free to get started.</p>
                <p>Once you\'ve signed up, take some time to explore the features and functionalities of our SaaS platform. Learn how it can benefit your business and streamline your operations.</p>
                <p>If you have any questions or need assistance, don\'t hesitate to reach out to our dedicated support team. We\'re here to help you make the most of our platform.</p>
                <p>Ready to unlock even more powerful features? Upgrade your plan to access premium functionalities tailored to your business needs.</p>
    
                <p>Thank you for choosing our SaaS platform. We\'re excited to have you on board, and we look forward to helping your business thrive in the digital age!</p>
    
                ',
                'attachment_id' => 44,
                'link' => env('APP_URL') . '/pages/content/get-started-with-out-saas-mobile-application',
                'text_color' => '#000000',
                'bg_color' => '#FFF6F6',
                'order' => 4,
                'style' => 4
            ],
            [
                'title' => 'HRM Management System',
                'slug' => 'hrm-system',
                'description' => 'HRM Gives You The Block & Components You Need To Create A Truly Professional Website For Your SaaS And Gives The Blocks.',
                'attachment_id' => 46,
                'link' => env('APP_URL') . '/pages/content/hrm-system',
                'text_color' => '#000000',
                'bg_color' => '#FAF8ED',
                'order' => 5,
                'style' => 1,
                'status_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Our Team',
                'slug' => 'our-team',
                'description' => 'Our Team is a group of individuals working together to achieve shared goals, combining their unique skills and expertise for collective success',
                'attachment_id' => 47,
                'link' => env('APP_URL') . '/pages/content/our-team',
                'text_color' => '#000000',
                'bg_color' => '#f9f9f9',
                'order' => 6,
                'style' => 1
            ],
            [
                'title' => 'Contact Us for Support',
                'slug' => 'contact-us',
                'description' => 'If you ever encounter issues or have suggestions for improvement, please don\'t hesitate to contact us. 
                We value your feedback and are dedicated to providing the best possible experience.
                Have questions or need assistance? Contact our support team for help.',
                'attachment_id' => 48,
                'link' => env('APP_URL') . '/pages/content/contact-us',
                'text_color' => '#4f46e5',
                'bg_color' => '#F9F5F6',
                'order' => 7,
                'style' => 3
            ]
        ];
    }
}
