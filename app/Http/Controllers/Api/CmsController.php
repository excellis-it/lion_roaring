<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\EcclesiaAssociation;
use App\Models\Faq;
use App\Models\Footer;
use App\Models\Gallery;
use App\Models\HomeCms;
use App\Models\MemberPrivacyPolicy;
use App\Models\Newsletter;
use App\Models\Organization;
use App\Models\OrganizationCenter;
use App\Models\OurGovernance;
use App\Models\OurOrganization;
use App\Models\PmaTerm;
use App\Models\PrincipalAndBusiness;
use App\Models\PrincipleBusinessImage;
use App\Models\PrivacyPolicy;
use App\Models\SiteSetting;
use App\Models\TermsAndCondition;
use App\Transformers\EcclesiaAssociationTransformers;
use App\Transformers\FaqTransformers;
use App\Transformers\FooterTransformers;
use App\Transformers\GalleryTransformers;
use App\Transformers\HomeTransformers;
use App\Transformers\OrganizationCenterTransformers;
use App\Transformers\OrganizationTransformers;
use App\Transformers\OurGovernanceTransformers;
use App\Transformers\PrincipalTransformers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Spatie\Fractalistic\Fractal;
use App\Mail\NewsletterSubscription;
use App\Models\AboutUs;
use App\Models\Detail;

/**
 * @group CMS Management
 */
class CmsController extends Controller
{
    protected $successStatus = 200;

    /**
     * Get gallery
     * @response 200{
     * "message": "Gallery",
     * "status": true,
     * "gallery": [
     *     {
     *       "image_url": "http://127.0.0.1:8000/storage/gallery/kg4vGiEpej4HXuc50ZVKQC2Agjvn6wj3rMygAOxu.jpg"
     *     },
     *   ]
     * }
     * @response 201 {
     * "message": "No gallery found",
     * "status": false
     * }
     */

    public function gallery(Request $request)
    {
        try {
            $galleries = Gallery::orderBy('id', 'desc')->get();
            if ($galleries) {
                $galleries = fractal($galleries, new GalleryTransformers())->toArray()['data'];
                return response()->json(['message' => 'Gallery', 'status' => true, 'gallery' => $galleries], $this->successStatus);
            } else {
                return response()->json(['message' => 'No gallery found', 'status' => false], 201);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 401);
        }
    }

    /**
     * Get FAQ
     *
     * @response 200{
     * "message": "FAQ",
     * "status": true,
     * "faq": [
     *    {
     *      "title": "Are there any transport facilities available for the staff members?",
     *      "content": "This is the second item's accordion body. It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the .accordion-body, though the transition does limit overflow."
     *   },
     * ]
     * }
     * @response 201 {
     * "message": "No FAQ found",
     * "status": false
     * }
     */

    public function faq(Request $request)
    {
        try {
            $faqs = Faq::orderBy('id', 'desc')->get();
            if ($faqs) {
                $faqs = fractal($faqs, new FaqTransformers())->toArray()['data'];
                return response()->json(['message' => 'FAQ', 'status' => true, 'faq' => $faqs], $this->successStatus);
            } else {
                return response()->json(['message' => 'No FAQ found', 'status' => false], 201);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 401);
        }
    }

    /**
     * Get Principal and Business Model
     * @response 200{
     * "message": "Principal and Business",
     * "status": true,
     * "principleAndBusiness": {
     *   "banner_image_url": "http://127.0.0.1:8000/storage/principle-and-business/iJt84RS9xLxfytBLynRBg4huIy9HfhfHWwJXEftc.jpg",
     *   "banner_title": "OUR PRINCIPLE AND BUSINESS MODEL",
     *   "image_url": "http://127.0.0.1:8000/storage/principle-and-business/CzFIynj2rOY7JSYtOVt1KtIKMFxFrP2A38J7Hwpz.png",
     *   "title": "OUR PRINCIPLE AND BUSINESS MODEL",
     *   "content": "<p><strong>These seven primodial spirits in God is the Core principle that Lion Roaring has adopted in order to pursue perfected love in our soul and become like Christ. And the ultimate goal in Lion Roaring is to be able to bring Heaven on Earth.</strong>
     * }
     * }
     * @response 201 {
     * "message": "No Principal and Business found",
     * "status": false
     * }
     */

    public function principleAndBusiness(Request $request)
    {
        try {
            $principleAndBusiness = PrincipalAndBusiness::orderBy('id', 'desc')->first();
            if ($principleAndBusiness) {
                $principleAndBusiness = fractal($principleAndBusiness, new PrincipalTransformers())->toArray()['data'];
                $principle_images = PrincipleBusinessImage::get();
                return response()->json(['message' => 'Principal and Business', 'status' => true, 'principleAndBusiness' => $principleAndBusiness, 'principle_images' => $principle_images], $this->successStatus);
            } else {
                return response()->json(['message' => 'No Principal and Business found', 'status' => false], 201);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 401);
        }
    }

    /**
     * Get Ecclesia Associations
     * @response 200{
     * "message": "Ecclesia Associations",
     * "status": true,
     * "ecclesiaAssociations": {
     *   "banner_image_url": "http://127.0.0.1:8000/storage/principle-and-business/iJt84RS9xLxfytBLynRBg4huIy9HfhfHWwJXEftc.jpg",
     *   "banner_title": "OUR ECCLESIA ASSOCIATION",
     *   "title": "OUR ECCLESIA ASSOCIATION",
     *   "content": "<p><strong>These seven primodial spirits in God is the Core principle that Lion Roaring has adopted in order to pursue perfected love in our soul and become like Christ. And the ultimate goal in Lion Roaring is to be able to bring Heaven on Earth.</strong>
     *   }
     * }
     * @response 201 {
     * "message": "No Ecclesia Associations found",
     * "status": false
     * }
     */

    public function ecclesiaAssociations(Request $request)
    {
        try {
            $ecclesiaAssociations = EcclesiaAssociation::orderBy('id', 'desc')->first();
            if ($ecclesiaAssociations) {
                $ecclesiaAssociations = fractal($ecclesiaAssociations, new EcclesiaAssociationTransformers())->toArray()['data'];
                return response()->json(['message' => 'Ecclesia Associations', 'status' => true, 'ecclesiaAssociations' => $ecclesiaAssociations], $this->successStatus);
            } else {
                return response()->json(['message' => 'No Ecclesia Associations found', 'status' => false], 201);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 401);
        }
    }

    /**
     * Get Organization
     * @response 200{
     * "message": "Organization",
     * "status": true,
     * "organization": {
     *"banner_section": {
     *"banner_image_url": "http://127.0.0.1:8000/storage/organization/paSVDc0EoYdGYGzvDpbK3akKyGkpEQII5SXIKt9c.jpg",
     *"banner_title": "OUR ORGANIZATION"
     *},
     *"about_section": {
     *"about_section_image_1": "http://127.0.0.1:8000/storage/organization/ygqFzHisBYM8pAoWIWf4WJuK0QIP6KjGTEHGfpgy.png",
     *"about_section_image_2": "http://127.0.0.1:8000/storage/organization/WqrUEHEAP8nsL4kRC0Qr2zo4tyI9cKVSjcQeWUG5.png",
     *"about_section_image_3": "http://127.0.0.1:8000/storage/organization/gNMs84RgkxLAcYAFlAq06YE3BHexYN0IeOZHADIO.png",
     *"about_section_image_4": "http://127.0.0.1:8000/storage/organization/UvaKMc7pcWKDGxTN20MF1aTK8wmfZPRH2LA1XLbE.png",
     *"about_section_image_5": "http://127.0.0.1:8000/storage/organization/Btj1lC8MDyDrn5oVwoJRfZarYsYQefTin5wIJlyl.png"
     *},
     *"project_section": {
     *"project_section_title": "LION ROARING INNOVATION CENTERS OVERVIEW",
     *"project_section_sub_title": "KINGSHIP AUTHORITY",
     *"project_section_description": "Lion Roaring Innovation Centers"
     *},
     *"project_section_details": [
     *{
     *    "title": "Science & Technology Innovation Center",
     *    "details": "<ul><li>Digital Transformation</li><li>Quantum Technology</li><li>BioTech</li><li>Telecommunication</li></ul><p><br>&nbsp;</p>"
     *},
     *{
     *    "title": "Healthcare & Medicine Innovation Center",
     *    "details": "<ul><li>Miracle Healing</li><li>Oil &amp; Herb Medicine</li><li>Natural Nutrition Intake</li></ul><p><br>&nbsp;</p>"
     *},
     *{
     *    "title": "Farming, Food & Nutrition Innovation Center",
     *    "details": "<ul><li>Agriculture</li><li>Distribution</li><li>Preserve &amp; Packaging</li><li>Farming</li></ul><p><br>&nbsp;</p>"
     *},
     *{
     *    "title": "Lion Roaring Foundation",
     *    "details": "<ul><li>US National Status</li><li>Change our Status</li><li>Training &amp; mentorship</li><li>Restoration of cities &amp; State via Education</li><li>Private Bank, Trust, 1099a, W4, etc.</li></ul><p><br>&nbsp;</p>"
     *},
     *{
     *    "title": "Animal Shelter & Training Innovation Center",
     *    "details": "<ul><li>Training</li><li>Rescue/Safari</li><li>Boarding</li></ul><p><br>&nbsp;</p>"
     *},
     *{
     *    "title": "Music, Media, Innovation Center",
     *    "details": "<ul><li>Music/Worship &amp; Arts</li><li>Harp &amp; Shofar, others</li><li>Dancing &amp; Painting</li></ul><p><br>&nbsp;</p>"
     *},
     *{
     *    "title": "Entertainment & Arts Innovation Center",
     *    "details": "<ul><li>Video &amp; Photo</li><li>Media &amp; Entertainment</li><li>Marketing &amp; Publisher</li></ul><p><br>&nbsp;</p>"
     *}
     *]
     *}
     * }
     * @response 201 {
     * "message": "No Organization found",
     * "status": false
     * }
     */

    public function ourOrganization(Request $request)
    {
        try {
            $organization = Organization::orderBy('id', 'desc')->first();
            if ($organization) {
                $organization = fractal($organization, new OrganizationTransformers())->toArray()['data'];
                return response()->json(['message' => 'Organization', 'status' => true, 'organization' => $organization], $this->successStatus);
            } else {
                return response()->json(['message' => 'No organization details found', 'status' => false], 201);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 401);
        }
    }

    /**
     * Get Common
     * @response 200{
     * "message": "Footer",
     * "status": true,
     * "footer": {
     * "Footer_logo": "http://127.0.0.1:8000/storage/footer/N7wWVhVjwtVyLl3uEDZZcAgCIqCsS3IZ4yaUl2tC.png",
     *    "Footer_description": "Our main focus is to restore our various communities, villages, cities, states, and our nation by restoring the condition of a person in both the spiritual and the physical.",
     *    "Footer_social_section": {
     *        "fa-brands fa-facebook-f": "https://facebook.com/login/",
     *        "fa-brands fa-instagram": "https://www.instagram.com/accounts/login/",
     *        "fa-brands fa-twitter": "https://twitter.com/i/flow/login?redirect_after_login=%2Flogin%3Flang%3Den",
     *        "fa-solid fa-envelope": "https://www.sealedenvelope.com/help/access/access/"
     *    },
     *    "Footer_address": "Lion Roaring",
     *    "Footer_address_details": "1070 20906 Frederick Rd STE A\r\n\r\nGermantown, MD 20876",
     *    "Footer_phoneno": "+1 (240)-982-0054",
     *    "Footer_Emailid": "info@localhost",
     *    "Footer_form_title": "Don’t miss our newsletter! Get in touch today!",
     *    "Footer_copywrite_text": "Copyright © 2024 Daud Santosa. All Rights Reserved"
     *   }
     * }
     * @response 201 {
     * "message": "No Footer found",
     * "status": false
     * }
     */

    public function common(Request $request)
    {
        try {
            $footer = Footer::orderBy('id', 'desc')->first();
            if ($footer) {
                $footer = fractal($footer, new FooterTransformers())->toArray()['data'];
                return response()->json(['message' => 'Footer details', 'status' => true, 'footer' => $footer], $this->successStatus);
            } else {
                return response()->json(['message' => 'No footer details found', 'status' => false], 201);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 401);
        }
    }

    /**
     * Get Home Page
     *
     * @response 200{
     * "message": "Home",
     * "status": true,
     *  "home": {
     *      "banner_title": "A habitation where supernatural and solution intersects",
     *      "banner_image": "http://127.0.0.1:8000/storage/home/rO06dZNGSicDTKgVPTGNc0DD6IDsliLBkuiu7cAm.png",
     *      "section_1_title": "ABOUT",
     *      "section_1_sub_title": "LION ROARING, PMA",
     *       "section_1_description": "Lion Roaring Private Members Association’s (PMA) main focus is to bring Heaven’s cultures on earth and to restore nations (communities, cities, states, and countries) through each soul whom the Lord has transformed and chosen. This soul will be given the opportunity to work for the Lion Roaring Innovation Centers and its partners being fully educated through Lion Roaring Foundation as God’s king and priest. The goal for each soul is to create inspired ideas and become self-sufficient to perform good works and bring heaven’s attributes into its environment.",
     *       "section_1_video": "home/P0aOPJfaUKa2ffn1bgaQxWZ6ij2NY2p8lBtylezK.mp4",
     *       "section_2_title_1": "Daud Santosa",
     *       "section_2_description_1": "Daud Santosa has over 35 years of experience as a transformation leader in the IT industry for establishing the corporate technology vision and leading all the aspects of the corporate technology deployment and development in both Government and Industry sectors. Responsibilites included transforming organization, people, and technologies to create new products and services. These new products and services, including modernization of infrastructures, helped establish new business services in the digital world, create new IT organizations which helped develop new cost models for Enterprise Data Inventory Strategy, Shared Services Centers, Cloud Computing services, and IT methodology. This led to a balance of score cards, performance metrics/Service Level Agreements, and business processes automation in areas of Financial, Human Resources, Acquisition, Law Enforcement, Telecommunications, Research Survey, and IT Hosting Services. Daud has held many different roles ranging from software engineer, Certified Chief IT Lead, IT Executive, and Chief Technology Officer. He has managed and had oversight over the IT budget within the range of $75,000,000 up to $2,000,000,000.\r\nThe Lord transformed Daud in 2016 after undergoing and surviving his third brain surgery at the end of 2015. He began seeking the Lord in 2016 by waiting on God every day from 3:00 to 4:30 AM. He had many spiritual encounters through dreams and visions in 2016-2017. He saw Jesus’s face in the 3rd Dimension when he was on Mt. Sinai. He also attended the Open Heaven Prophetic Conference with Prophet Sadhu Selvaraj in 2017. Since then, his life has changed as he regularly walkiswith God. He continues to experience many visions, dreams, and revelations. The Lord has taught him over the past two years as a leader under JMK Maryland (a branch of Jesus My King Church, Shelby, North Carolina) to prepare his congregation to become a wise Sower with Kingship and Priesthood anointing.\r\nAlos, during this time, he received the strategy of Lion Roaring (Kingship Authority) and established JMK Maryland (Priesthood authority) through divine intervention of the Holy Spirit as he connected with Pastor Michael Widjaya and Dr. Steven Francis. This is God’s destiny as revealed to Daud and his wife; that is to establish Kingship and Priesthood authority where Heaven is brought on earth with the office of Christ, which is the office of the everlasting Kingdom of Light.\r\nCurrently, he is the elder of JMK Maryland that helps that facilitate Lion Roaring teachings on Spiritual and Leadership Development by helping to restore one person, group, community, and nation at the time as the Lord directs him.",
     *       "section_2_image_1": "http://127.0.0.1:8000/storage/home/9A3tUlCktNEqIeXMUpU2014sZQXBsSeaiQFKigqG.jpg",
     *       "section_2_title_2": "Lystia Santosa",
     *       "section_2_description_2": "Lystia Santosa has over 35 years of experience working in the financial and accounting field for International Non-profit Organizations. She has held various positions during her career such as an auditor for a CPA Firm auditing the Federal Government Grant Programs, a Field Office Project Accountant and Senior Accountant, Accounting Manager, Controller, and then a Director of Finance position. Although Lystia obtained her CPA (Certified Public Accountant) early in her career, she decided not to pursue a career in the Public Accounting but to devote her career working for an international non-profit organization working with the third world countries. It was her passion to work in an organization in which the primary mission and the vision were to help people in Third World countries to improve their standard of living.\r\nPrior to her retirement in 2019, she held a position as the Director of Finance (CFO) working for the largest U.S. based international worker rights organization. She helped facilitate the organization’s mission of helping workers attain safe and healthy workplaces, while promoting worker’s equality. She also helped improve workers’ standard of living with education and collective agreement, and by helping fight discrimination, and by pr eventing the exploitation of systems that entrench poverty. Her 30 plus years’ experience working with this organization gave her solid and broad mastery in all areas in financial management, financial affairs, budgeting, human resources and personnel policy and procedures. Furthermore, she developed expertise to ensure compliance with U.S. Federal Rules & regulations on grant awards, and how to effectively deal with the the organization’s funders (U.S. Government, foundation and international donors). She was responsible for the organization’s annual budget of about $32,000,000.00 and directed staff in the finance and accounting departments at the company’s headquarters, and approximately 30 filed offices. Additionally, she was a member of the Executive Team, and she worked closely with the CEO, COO and other Directors in the implementation of the organization’s vision and mission.\r\nSince retiring in 2019, Lystia has been volunteering her time in helping JMK Shelby, North Carolina church with their accounting and financial matters. She is also the elder for JMK Maryland church alongside her husband, Daud Santosa.\r\nDuring the pandemic, the Lord brought her into a more intimate relationship with Him and helped train her to study His words on a deeper level. This helped shift her priorities from working from a worldly employer to working for God’s Kingdom. She gave up her consulting work and completely devoted her time to studying and working alongside with husband, Daud Santosa in serving the JMK Maryland church.",
     *       "section_2_image_2": "http://127.0.0.1:8000/storage/home/7lAhy6PtApuL5YXfPFjtrjpCyvejIh9VadgqBJHY.jpg",
     *       "section_3_title": "OUR GOVERNANCE BOARD",
     *       "section_3_description": "THIS BOARD PROVIDES DIRECTION AND OVERSIGHT FOR DAY-TO-DAY OPERATION OF LION ROARING, PMA.",
     *       "section_4_title": "OUR ORGANIZATION",
     *       "section_4_description": "A habitation where supernatural and solution intersects",
     *       "section_5_title": "TESTIMONIES",
     *       "our_governance": [
     *           {
     *               "slug": "robert-hyde",
     *               "name": "ROBERT HYDE",
     *               "image": "http://127.0.0.1:8000/storage/our_governances/D4RVbxeNVJensk62eVAffWqIKE0hm57GxwzztEhy.jpg"
     *           },
     *           {
     *               "slug": "daud-santosa",
     *               "name": "DAUD SANTOSA",
     *               "image": "http://127.0.0.1:8000/storage/our_governances/f2rEvSGf6P47ASO8jQJIWu5PefAMd8FI1GfWaK6x.png"
     *           }
     *       ],
     *       "our_organization": [
     *           {
     *               "slug": "lion-roaring-innovation-center",
     *               "name": "Lion Roaring Innovation Center",
     *               "image": "http://127.0.0.1:8000/storage/our_organizations/RoFGvUKvvRXGbbpMmlp9xUt00pICuBwVYHlFMOVN.jpg",
     *               "description": "The mission of Lion Roaring innovation center is building the future of innovation technologies to support the vision of Lion Roaring and to support natural habitation that follows Psalm 104:14-18, 24-25 – “God cause grass to grow for the cattle, herb for the service of man: bring forth food out of the earth; and wine that makes glad the heart of man, and oil to make his face to shine, and bread which strengthens mean’s heart. In wisdom God made them all: the earth full of your riches”. This innovation will be leveraged to help restore villages, cities, states, and nations through Lion Roaring Education Centers."
     *           },
     *           {
     *               "slug": "lion-roaring-education-center-1709707179",
     *               "name": "Lion Roaring Education Center",
     *               "image": "http://127.0.0.1:8000/storage/our_organizations/Vuqu2PVoVJkJJB03Aq9UcE9Kd9XPGsZNRFeH186f.jpg",
     *               "description": "The mission of Lion Roaring Education Centers (LREC) is to educate each person to embrace the kingdom of God by restoring the soul through the salvation of the Lord Jesus. In doing so, LREC will also help develop spiritual maturity through spiritual growth and transformation. And to nurture those skills according to that person’s giftedness within the circle of the Lion Roaring Community of interest groups and within the Lion Roaring Habitation and partnership around the world."
     *           }
     *       ],
     *       "testimonial": [
     *           {
     *               "image": "http://127.0.0.1:8000/storage/testimonials/czmnEq1ldlivCk3oDi6GU9PX09etwMMJhScEziDA.jpg",
     *               "description": "Praise the Lord,\r\nGreeting to you in the name of loving Lord Savior Jesus!\r\nThe Lord has chosen you for His royal work anointed you and filled you with abundant grace. God the Father has anointed you with a special anointing of word of understanding and word of wisdom, word of knowledge and the Holy Spirit. He has anointed you to know the truth of the word quickly and has given many spiritual gifts, and God has laid on your hands a fivefold ministry.\r\nI thank God for you and the ministry, God has sent and burdened you to help us both physically and spiritually. The villa of Orissa has benefited from many donations we have received from you and your ministry.\r\n1. Monthly donation for Pastors. With your help, the Lord’s work is going on in Orissa today. The Gospel is being preached, and today 15 pastors/ preachers have received benefit from your monthly donation. They are preaching the gospel in the sparsely populated areas and able to minister to the villages through Sunday service or visitation."
     *           },
     *           {
     *               "image": "http://127.0.0.1:8000/storage/testimonials/2lb6NeSg91X92iHhVAnvHBNjIHTCmzpdtrcZNiP8.jpg",
     *               "description": "I thank the Lord for the partnership with the Lion Roaring for 3 years ago, I have been doing outreaches and bible study that time when servant of God Brother Daud Santosa and Family help me in my financial needs, it almost lasted for 3 years. The Santosa Family and ministry partners supported me that leads to formation of planting. I have been an independent worker that time and with the help of our ministry partners I was able to in large our border. Thank God for the Santosa Family and friends."
     *           }
     *       ],
     *       "gallery": [
     *           {
     *               "image": "http://127.0.0.1:8000/storage/gallery/evLHH1jyrA0QMAf8zTFT0ySjA1M7UDWjd15swPr9.jpg"
     *           },
     *           {
     *               "image": "http://127.0.0.1:8000/storage/gallery/lkkpoCQcQy6wEcl9kppNZFqHEwpQklQU0ck87BcC.jpg"
     *           }
     *       ]
     *   }
     */

    public function home(Request $request)
    {
        try {
            $home = HomeCms::orderBy('id', 'desc')->first();
            if ($home) {
                $home = fractal($home, new HomeTransformers())->toArray()['data'];
                return response()->json(['message' => 'home', 'status' => true, 'home' => $home], $this->successStatus);
            } else {
                return response()->json(['message' => 'No home deatils found', 'status' => false], 201);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 401);
        }
    }

    /**
     * Get Our Governance
     * @bodyParam slug string required The slug of the our governance. Example: robert-hyde
     * @response 200{
     * "message": "Our Governance",
     * "status": true,
     * "governance": {
     *     "banner_title": "ROBERT HYDE",
     *     "banner_image": "http://127.0.0.1:8000/storage/our_governances/8VC1259WpyYkRPUjfqYVBjiD85vvGpNuLcR0fiRG.jpg",
     *     "name": "ROBERT HYDE",
     *     "description": "Bob Hyde spent 16 years’ working in the education field as a middle school teacher for subjects of United States and world history. A love of history and the joy of working with children made this a good fit for his career change. Putting aside the content teaching aspect of this profession, most importantly, he learned the importance of relationship building with his students. Without the relationships in place, no teaching will be effective. He also learned the value of cooperative learning and “hands on” or learning by doing, while striving to make learning fun as key components for student engagement and learning. Bob used these strategies as his focus in his teaching and programs like Peer Tutoring, Homework Club, and the National Junior Honor Society that he led throughout his career.\r\nPrior to working in the education field, Bob spent over 20 years in the business world working as a team manager in the insurance industry. Responsibilities included overseeing high dollar claims settlements, personnel management, and customer service. One key takeaway from this profession was the ability to successfully communicate with various stakeholders (superiors, peers, subordinates, customers). Learning skills like compassion and simply listening to others’ needs and desires went a long way to him being able to establish rapport and successful working relationships with all stakeholders.\r\nBob retired from teaching in 2022 and has since concentrated on learning America’s “true history,” his rightful status as a united States citizen, loving on and assisting family members through trying circumstances, and by learning and relying on God to become more intimate with his Creator. Individual study and prayer, and teachings from JMK Maryland and Lion Roaring have all helped with Bob’s spiritual growth. While assisting other leaders as a Board Member of Lion Roaring, Bob is looking forward to his next journey with Lion Roaring and direction from the Lord how best to use him.",
     *     "image_url": "http://127.0.0.1:8000/storage/our_governances/D4RVbxeNVJensk62eVAffWqIKE0hm57GxwzztEhy.jpg"
     *    }
     * }
     */

    public function ourGovernance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required|exists:our_governances,slug',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'status' => false], 201);
        }

        try {
            $governance = OurGovernance::where('slug', $request->slug)->first();
            if ($governance) {
                $governance = fractal($governance, new OurGovernanceTransformers())->toArray()['data'];
                return response()->json(['message' => 'Our governance', 'status' => true, 'governance' => $governance], $this->successStatus);
            } else {
                return response()->json(['message' => 'No governance found', 'status' => false], 201);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 401);
        }
    }

    /**
     * Our organization center
     * @bodyParam slug string required The slug of the our governance. Example: lion-roaring-innovation-center, lion-roaring-education-center-1709707179.
     * @response 200{
     * "message": "Organization Center",
     * "status": true,
     * "our_organization_centers": [
     *    {
     *        "id": 4,
     *        "our_organization_id": 1,
     *        "name": "LION ROARING FOUNDATION",
     *        "slug": "lion-roaring-foundation",
     *        "description": "LION ROARING FOUNDATION",
     *        "banner_image": "organization_centers/ItTjpXfo45xddk8eWYgcmCO4mlzj1ZlVCSf9wWDq.jpg",
     *        "image": "organization_centers/9Fxa0aHJcLSvAdzdSQ7tKb5vfgW4JxXfB44r7Czf.jpg",
     *        "meta_title": "LION ROARING FOUNDATION",
     *        "meta_description": "LION ROARING FOUNDATION",
     *        "meta_keywords": "LION ROARING FOUNDATION",
     *        "created_at": "2024-03-06T10:37:18.000000Z",
     *        "updated_at": "2024-03-06T10:37:18.000000Z"
     *    }
     * }
     */

    public function organizationCenter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required|exists:our_organizations,slug'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'status' => false], 201);
        }

        try {
            $our_organizatiion = OurOrganization::where('slug', $request->slug)->first();
            $our_organization_centers = OrganizationCenter::where('our_organization_id', $our_organizatiion->id)->orderBy('id', 'desc')->get();

            if ($our_organization_centers) {
                return response()->json(['message' => 'Organization center', 'status' => true, 'our_organization_centers' => $our_organization_centers], $this->successStatus);
            } else {
                return response()->json(['message' => 'No organization center found', 'status' => false], 201);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 401);
        }
    }

    /**
     * Organization center details
     * @bodyParam slug string required The slug of the organization center. Example: lion-roaring-foundation.
     * @response 200{
     *  "message": "Organization center details",
     *   "status": true,
     *   "details": {
     *       "banner_title": "LION ROARING FOUNDATION",
     *       "banner_image": "http://127.0.0.1:8000/storage/organization_centers/ItTjpXfo45xddk8eWYgcmCO4mlzj1ZlVCSf9wWDq.jpg",
     *       "name": "LION ROARING FOUNDATION",
     *       "description": "LION ROARING FOUNDATION",
     *       "image_url": "http://127.0.0.1:8000/storage/organization_centers/9Fxa0aHJcLSvAdzdSQ7tKb5vfgW4JxXfB44r7Czf.jpg"
     *   }
     * }
     */

    public function organizationCenterDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required|exists:organization_centers,slug'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'status' => false], 201);
        }

        try {
            $details = OrganizationCenter::where('slug', $request->slug)->first();
            if ($details) {
                $details = fractal($details, new OrganizationCenterTransformers())->toArray()['data'];
                return response()->json(['message' => 'Organization center details', 'status' => true, 'details' => $details], $this->successStatus);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 401);
        }
    }

    /**
     * Member Privacy Policy
     * @response 200{
     * "message": "Privacy Policy",
     * "status": true,
     * "privacy_policy": {
     *    "id": 1,
     *    "description": "<p><strong>Privacy Policy for Member Accounts on My Lion Roaring Website</strong></p><p>At My Lion Roaring, we are committed to protecting the privacy and security of our members' personal information. This Privacy Policy outlines how we collect, use, disclose, and protect your information when you create and use an account on our website. By becoming a member, you agree to the terms outlined in this policy.</p><p><strong>Information We Collect:</strong></p><p><strong>Account Information:</strong> When you create an account, we collect your name, email address, username, password, and any other information you choose to provide, such as profile pictures or biographical details.</p><p><strong>Usage Data:</strong> We automatically collect information about how you interact with our website, including your IP address, browser type, device information, pages visited, and timestamps of your visits.</p><p><strong>Communication Data:</strong> If you contact us through our website or via email, we may keep a record of that communication, including the content and metadata.</p><p><strong>How We Use Your Information:</strong></p><p><strong>Account Management:</strong> We use your account information to manage your membership, provide access to our services, and personalize your experience on our website.</p><p><strong>Communication:</strong> We may use your contact information to send you important updates, newsletters, promotional offers, and other communications related to our services. You can opt out of these communications at any time.</p><p><strong>Analytics and Improvements:</strong> We analyze usage data to improve our website, enhance user experience, and optimize our services.</p><p><strong>Information Sharing and Disclosure:</strong></p><p><strong>Service Providers:</strong> We may share your information with third-party service providers who assist us in operating our website, conducting business, or providing services to you. These providers are contractually obligated to protect your information and use it only for authorized purposes.</p><p><strong>Legal Compliance:</strong> We may disclose your information if required to do so by law, regulation, or legal process, or if we believe that disclosure is necessary to protect our rights, property, or safety, or the rights, property, or safety of others.</p><p><strong>Data Security:</strong></p><p>We take appropriate measures to protect your information from unauthorized access, alteration, disclosure, or destruction. However, please note that no method of transmission over the internet or electronic storage is 100% secure, and we cannot guarantee absolute security.</p><p><strong>Your Choices and Rights:</strong></p><p><strong>Access and Update:</strong> You can access and update your account information at any time by logging into your account settings.</p><p><strong>Data Removal:</strong> You can request the deletion of your account and associated information by contacting us. However, please note that some information may be retained as necessary for legal or legitimate business purposes.</p><p><strong>Marketing Preferences:</strong> You can manage your communication preferences and opt out of marketing communications through your account settings or by contacting us.</p><p><strong>Changes to This Policy:</strong></p><p>We reserve the right to update or modify this Privacy Policy at any time. We will notify you of any significant changes by posting the updated policy on our website or by other means of communication.</p><p><strong>Contact Us:</strong></p><p>If you have any questions, concerns, or requests regarding your privacy or this Privacy Policy, please contact us at <a href=\"http://127.0.0.1:8000/contact-us\">Click</a>.</p><p><br>&nbsp;</p>",
     *    "created_at": "2024-04-24T13:15:57.000000Z",
     *    "updated_at": "2024-04-25T05:27:23.000000Z"
     * }
     * }
     *
     * @response 201 {
     * "message": "No privacy policy found",
     * "status": false
     * }
     *
     */

    public function membersPrivacyPolicy(Request $request)
    {
        try {
            $privacy_policy = MemberPrivacyPolicy::orderBy('id', 'desc')->first();
            if ($privacy_policy) {
                return response()->json(['message' => 'Privacy Policy', 'status' => true, 'privacy_policy' => $privacy_policy], $this->successStatus);
            } else {
                return response()->json(['message' => 'No privacy policy found', 'status' => false], 201);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 401);
        }
    }

    /**
     * PMA Disclaimer Policy
     *
     * This endpoint returns the latest PMA Disclaimer Policy term including title and description.
     * It fetches the latest record from the `pma_terms` table.
     *
     * @response 200 scenario="Term found" {
     *   "message": "PMA Term",
     *   "status": true,
     *   "term": {
     *     "title": "Sample PMA Term Title",
     *     "description": "Detailed description of the PMA term."
     *   }
     * }
     *
     * @response 201 scenario="No term found" {
     *   "message": "No PMA Term found",
     *   "status": false
     * }
     *
     * @response 401 scenario="Server error" {
     *   "message": "Error message from exception",
     *   "status": false
     * }
     */

    public function pmaDisclaimerPolicy(Request $request)
    {
        try {
            $term = PmaTerm::orderBy('id', 'desc')->select('title', 'description')->first();
            if ($term) {
                return response()->json(['message' => 'PMA Term', 'status' => true, 'term' => $term], $this->successStatus);
            } else {
                return response()->json(['message' => 'No PMA Term found', 'status' => false], 201);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 401);
        }
    }


    /**
     * Get Privacy Policy
     *
     * Returns the latest privacy policy content.
     *
     * @response 200 {
     *   "message": "privacy policy",
     *   "status": true,
     *   "privacy_policy": {
     *     "title": "Privacy Policy Title",
     *     "description": "Privacy Policy Description"
     *   }
     * }
     * @response 201 {
     *   "message": "No privacy policy found",
     *   "status": false
     * }
     * @response 401 {
     *   "message": "Error message",
     *   "status": false
     * }
     */
    public function privacy_policy()
    {
        try {
            $privacy_policy = PrivacyPolicy::orderBy('id', 'desc')->select('text', 'description')->first();
            if ($privacy_policy) {
                return response()->json(['message' => 'privacy policy', 'status' => true, 'privacy_policy' => $privacy_policy], $this->successStatus);
            } else {
                return response()->json(['message' => 'No privacy policy found', 'status' => false], 201);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 401);
        }
    }


    /**
     * Get Terms and Conditions
     *
     * Returns the latest terms and conditions content.
     *
     * @response 200 {
     *   "message": "Terms and Condition",
     *   "status": true,
     *   "term": {
     *     "title": "Terms Title",
     *     "description": "Terms Description"
     *   }
     * }
     * @response 201 {
     *   "message": "No Terms and Condition found",
     *   "status": false
     * }
     * @response 401 {
     *   "message": "Error message",
     *   "status": false
     * }
     */
    public function terms()
    {
        try {
            $term = TermsAndCondition::orderBy('id', 'desc')->select('text', 'description')->first();
            if ($term) {
                return response()->json(['message' => 'Terms and Condition', 'status' => true, 'term' => $term], $this->successStatus);
            } else {
                return response()->json(['message' => 'No Terms and Condition found', 'status' => false], 201);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 401);
        }
    }

    /**
     * Get the Article of Association PDF
     *
     * This endpoint returns the URL of the company's Article of Association PDF if available.
     *
     * @response 200 {
     *  "message": "Article of association",
     *  "status": true,
     *  "url": "https://example.com/path/to/article.pdf"
     * }
     *
     * @response 201 {
     *  "message": "No Article of association found",
     *  "status": false
     * }
     *
     * @response 401 {
     *  "message": "Error message",
     *  "status": false
     * }
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function article_of_association()
    {
        try {
            $pdf_url = Helper::getPDFAttribute();
            if ($pdf_url) {
                return response()->json(['message' => 'Article of association', 'status' => true, 'url' => $pdf_url], $this->successStatus);
            } else {
                return response()->json(['message' => 'No Article of association found', 'status' => false], 201);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 401);
        }
    }

    /**
     * Submit newsletter form
     *
     * This endpoint allows a user to submit a newsletter form. The request must include the name, email, and message.
     *
     * @bodyParam newsletter_name string required The full name of the subscriber. Example: John Doe
     * @bodyParam newsletter_email string required The subscriber's email address. Example: john@example.com
     * @bodyParam newsletter_message string required The message or feedback from the subscriber. Example: I'd love to hear more about your services!
     *
     * @response 200 {
     *   "message": "Newsletter submit successfully.",
     *   "status": true
     * }
     *
     * @response 401 {
     *   "message": "Error message details here",
     *   "status": false
     * }
     */
    public function newsletter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'newsletter_name' => 'required',
            'newsletter_email' => 'required|email',
            'newsletter_message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'status' => false], 201);
        }

        try {
            $newsletter = new Newsletter();
            $newsletter->full_name = $request->newsletter_name;
            $newsletter->email = $request->newsletter_email;
            $newsletter->message = $request->newsletter_message;
            $newsletter->save();

            $adminEmail = SiteSetting::first()->SITE_CONTACT_EMAIL;
            $mailData = [
                'name' => $newsletter->full_name,
                'email' => $newsletter->email,
                'message' => $newsletter->message,
            ];

            try {
                Mail::to($adminEmail)->send(new NewsletterSubscription($mailData));
            } catch (\Throwable $th) {
                return response()->json(['message' => 'Newsletter submit successfully.', 'status' => true], $this->successStatus);
            }

            return response()->json(['message' => 'Newsletter submit successfully.', 'status' => true], $this->successStatus);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 401);
        }
    }

    // aboutUs
    /**
     * Get About Us
     *
     * This endpoint retrieves the latest "About Us" content.
     *
     * @response 200 {
     *   "message": "About Us",
     *   "status": true,
     *   "about_us": {
     *     "title": "About Us Title",
     *     "description": "About Us Description"
     *   }
     * }
     *
     * @response 201 {
     *   "message": "No About Us found",
     *   "status": false
     * }
     *
     * @response 401 {
     *   "message": "Error message",
     *   "status": false
     * }
     */
    public function aboutUs()
    {
        try {
            $about_us = AboutUs::orderBy('id', 'desc')->select('banner_title', 'description')->first();
            if ($about_us) {
                return response()->json(['message' => 'About Us', 'status' => true, 'about_us' => $about_us], $this->successStatus);
            } else {
                return response()->json(['message' => 'No About Us found', 'status' => false], 201);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 401);
        }
    }

    // detailsPage with multi row data from model Detail
    /**
     * Get Details
     *
     * This endpoint retrieves the latest details.
     *
     * @response 200 {
     *   "message": "Details",
     *   "status": true,
     *   "details": [
     *     {
     *       "title": "Detail Title",
     *       "description": "Detail Description"
     *     }
     *   ]
     * }
     *
     * @response 201 {
     *   "message": "No details found",
     *   "status": false
     * }
     *
     * @response 401 {
     *   "message": "Error message",
     *   "status": false
     * }
     */
    public function detailsPage()
    {
        try {
            $details = Detail::orderBy('id', 'asc')->select('image', 'description')->get();
            if ($details) {
                return response()->json(['message' => 'Details', 'status' => true, 'details' => $details], $this->successStatus);
            } else {
                return response()->json(['message' => 'No details found', 'status' => false], 201);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 401);
        }
    }

    // get site settings

     public function siteSettings() {
         try {
             $settings = SiteSetting::first();
             if ($settings) {
                 return response()->json(['message' => 'Site Settings', 'status' => true, 'settings' => $settings], $this->successStatus);
             } else {
                 return response()->json(['message' => 'No Site Settings found', 'status' => false], 201);
             }
         } catch (\Throwable $th) {
             return response()->json(['message' => $th->getMessage(), 'status' => false], 401);
         }
     }
}
