-- Drop existing tables if they exist
DROP TABLE IF EXISTS `leads`;
DROP TABLE IF EXISTS `agent_connections`;
DROP TABLE IF EXISTS `site_visits`;
DROP TABLE IF EXISTS `consultations`;
DROP TABLE IF EXISTS `properties`;
DROP TABLE IF EXISTS `agents`;
DROP TABLE IF EXISTS `cities`;
DROP TABLE IF EXISTS `users`;

-- 1. Users Table
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `phone` VARCHAR(20) DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('buyer', 'seller', 'agent', 'admin', 'lawyer') DEFAULT 'buyer',
  `avatar` VARCHAR(255) DEFAULT NULL,
  `subscription_ends_at` TIMESTAMP NULL DEFAULT NULL,
  `enrollment_id` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Cities Table
CREATE TABLE `cities` (
  `id` VARCHAR(50) PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `property_count` INT DEFAULT 0,
  `lat` DECIMAL(10, 6) NOT NULL,
  `lng` DECIMAL(10, 6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Agents Table
CREATE TABLE `agents` (
  `id` VARCHAR(50) PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `rating` DECIMAL(3, 2) NOT NULL,
  `specialty` VARCHAR(100) NOT NULL,
  `image` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Properties Table
CREATE TABLE `properties` (
  `id` VARCHAR(50) PRIMARY KEY,
  `listing_id` VARCHAR(50) NOT NULL UNIQUE,
  `city_id` VARCHAR(50) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `type` VARCHAR(100) NOT NULL, -- Residential, Commercial, Industrial, Agricultural
  `category` VARCHAR(100) NOT NULL, -- Auction, Rental, Heavy Deposit, Seller Listed
  `address` TEXT NOT NULL,
  `reserve_price` VARCHAR(100) NOT NULL,
  `numeric_price` BIGINT NOT NULL,
  `emd` VARCHAR(100) NOT NULL,
  `government_valuation` VARCHAR(100) DEFAULT NULL,
  `numeric_gov_valuation` BIGINT DEFAULT NULL,
  `auction_date` DATETIME DEFAULT NULL,
  `bank` VARCHAR(100) DEFAULT 'N/A',
  `borrower` VARCHAR(100) DEFAULT 'N/A',
  `possession` VARCHAR(100) DEFAULT 'Ready to Move',
  `agent_id` VARCHAR(50) DEFAULT NULL,
  `image` VARCHAR(255) NOT NULL,
  `details` TEXT NOT NULL,
  `notice_english` TEXT DEFAULT NULL,
  `notice_marathi` TEXT DEFAULT NULL,
  `seller_id` INT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`city_id`) REFERENCES `cities`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`agent_id`) REFERENCES `agents`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`seller_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Legal Consultations
CREATE TABLE `consultations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `booking_date` DATE NOT NULL,
  `topic` VARCHAR(150) NOT NULL,
  `advocate_id` VARCHAR(50) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Site Inspections/Visits
CREATE TABLE `site_visits` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `property_id` VARCHAR(50) NOT NULL,
  `visit_date` DATE NOT NULL,
  `time_slot` VARCHAR(50) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `agent_id` VARCHAR(50) NOT NULL,
  `status` VARCHAR(50) DEFAULT 'Pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`property_id`) REFERENCES `properties`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`agent_id`) REFERENCES `agents`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Agent Contact Connections
CREATE TABLE `agent_connections` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `agent_id` VARCHAR(50) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `message` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`agent_id`) REFERENCES `agents`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. General Leads Table (Trial claims & Brochures)
CREATE TABLE `leads` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `campaign` VARCHAR(150) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ================= SEED DATA =================

-- Seed Cities
INSERT INTO `cities` (`id`, `name`, `property_count`, `lat`, `lng`) VALUES
('mumbai', 'Mumbai', 425, 19.0760, 72.8777),
('thane', 'Thane', 312, 19.2183, 72.9781),
('pune', 'Pune', 380, 18.5204, 73.8567),
('nashik', 'Nashik', 145, 20.0110, 73.7903),
('aurangabad', 'Aurangabad', 92, 19.8762, 75.3433),
('nagpur', 'Nagpur', 189, 21.1458, 79.0882),
('kolhapur', 'Kolhapur', 65, 16.7050, 74.2433),
('solapur', 'Solapur', 54, 17.6599, 75.9064),
('amravati', 'Amravati', 41, 20.9320, 77.7523),
('jalgaon', 'Jalgaon', 38, 21.0077, 75.5626),
('nanded', 'Nanded', 47, 19.1383, 77.3210);

-- Seed Agents
INSERT INTO `agents` (`id`, `name`, `phone`, `email`, `rating`, `specialty`, `image`) VALUES
('agt-1', 'Aniket Deshmukh', '+91 98230 12345', 'aniket.d@mahaauctions.com', 4.90, 'Bank Auctions', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=150&q=80'),
('agt-2', 'Meera Kulkarni', '+91 98901 67890', 'meera.k@mahaauctions.com', 4.80, 'Premium Rentals & Heavy Deposit', 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=150&q=80'),
('agt-3', 'Sanjay Patil', '+91 91582 34567', 'sanjay.p@mahaauctions.com', 4.70, 'Commercial & Industrial Listings', 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=150&q=80');

-- Seed Mock Sellers
-- (Password is hashed for 'password123')
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `avatar`) VALUES
(1, 'System Admin', 'admin@mahaauctions.com', '$2y$10$wE96clyeE512aXzVd74GKeGg1M0KxJbH7kKzI1q2s3t4u5v6w7x8y', 'admin', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=100&q=80');

-- Seed Properties
INSERT INTO `properties` (`id`, `listing_id`, `city_id`, `title`, `type`, `category`, `address`, `reserve_price`, `numeric_price`, `emd`, `government_valuation`, `numeric_gov_valuation`, `auction_date`, `bank`, `borrower`, `possession`, `agent_id`, `image`, `details`, `notice_english`, `notice_marathi`, `seller_id`) VALUES
('prop-1', 'MA-2026-001', 'mumbai', '3 BHK Sea-facing Apartment in Worli', 'Residential', 'Auction', 'A-Wing, 14th Floor, Sea View Heights, Worli, Mumbai', '₹ 4.50 Cr', 45000000, '₹ 45.00 Lakhs', '₹ 5.80 Cr', 58000000, '2026-06-15 11:00:00', 'State Bank of India', 'Rajesh Sharma', 'Physical', 'agt-1', 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80', 'Spacious 3 BHK apartment with unobstructed sea views. Building features premium amenities including pool, gym, and 24/7 security.', 'PUBLIC NOTICE FOR E-AUCTION: Under Sec 13(2) of SARFAESI Act, 2002. Notice is hereby given that the secured asset described herein will be sold on "As is where is basis" for recovery of bank dues of ₹4.12 Cr. Reserve Price: ₹4.50 Cr. EMD: ₹45 Lakhs.', 'ई-लिलाव जाहीर नोटीस: सिक्युरिटायझेशन कायद्याच्या कलम १३(२) अंतर्गत जाहीर नोटीस. याद्वारे कळविण्यात येते की बँकेच्या थकीत रु. ४.१२ कोटी वसुलीसाठी सदर मालमत्ता "आहे त्या स्थितीत" लिलावात काढण्यात येत आहे. राखीव किंमत: रु. ४.५० कोटी. इसारा ठेव (EMD): रु. ४५ लाख.', 1),

('prop-2', 'MA-2026-002', 'mumbai', 'Commercial Office Space in BKC', 'Commercial', 'Auction', 'Unit 402, 4th Floor, Platinum Tower, BKC, Mumbai', '₹ 12.00 Cr', 120000000, '₹ 1.20 Cr', '₹ 15.50 Cr', 155000000, '2026-06-20 10:30:00', 'ICICI Bank', 'TechVision Solutions Pvt Ltd', 'Symbolic', 'agt-3', 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=800&q=80', 'Prime commercial office space in Bandra Kurla Complex. Fully furnished with 50 workstations, 2 executive cabins, and 2 large conference rooms.', 'SALE NOTICE FOR SALE OF IMMOVABLE PROPERTY: under Rule 8(6) of Security Interest Rules. ICICI Bank hereby invites online bids for symbolic possession property at BKC. Dues: ₹10.85 Cr. Reserve Price: ₹12.00 Cr.', 'स्थावर मालमत्ता विक्री नोटीस: सिक्युरिटीं इंटरेस्ट नियमांच्या नियम ८(६) अन्वये विक्री नोटीस. आयसीआयसीआय बँक याद्वारे बीकेसी येथील मालमत्तेसाठी ऑनलाईन बोली मागवित आहे. राखीव किंमत: रु. १२.०० कोटी. इसारा ठेव: रु. १.२० कोटी.', 1),

('prop-3', 'MA-2026-003', 'mumbai', '2 BHK Premium Flat - Heavy Deposit', 'Residential', 'Heavy Deposit', 'Sector 4, Hiranandani Meadows, Thane West, Mumbai', '₹ 25.00 Lakhs (Deposit Only)', 2500000, 'N/A', '₹ 32.00 Lakhs (Normal Market Deposit Equivalent)', 3200000, NULL, 'N/A (Seller Listed)', 'N/A', 'Ready to Move', 'agt-2', 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80', 'Classic Heavy Deposit option. Pay ₹25 Lakhs refundable deposit with ZERO monthly rent for 2 years. Premium semi-furnished flat in Hiranandani with modern kitchen and high-speed elevator.', 'HEAVY DEPOSIT AGREEMENT: Secure agreement of tenure 24 months. Refundable security deposit of ₹25,000,000 paid at registration. Agreement registered via government portal.', 'हेवी डिपॉझिट करारनामा: २४ महिन्यांच्या कालावधीसाठी सुरक्षित करार. नोंदणीच्या वेळी २५,००,००० रुपये परतावायोग्य ठेव रक्कम. शासकीय नोंदणीकृत भाडे करारनामा केला जाईल.', 1),

('prop-4', 'MA-2026-004', 'pune', '4 BHK Luxury Villa for Rent in Koregaon Park', 'Residential', 'Rental', 'Villa No. 7, Silver Oaks Society, Koregaon Park, Pune', '₹ 1.80 Lakhs / Month', 180000, 'N/A', '₹ 2.20 Lakhs / Month (Market Avg Rent)', 220000, NULL, 'N/A', 'N/A', 'Immediate Occupancy', 'agt-2', 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80', 'Independent luxury villa for rent. Private manicured garden, swimming pool, imported modular kitchen, servant quarters, and solar panel grid installed.', 'LEASING PUBLIC OPPORTUNITY: Luxury Pune residency open for corporate or family lease. Terms are negotiable with 3 months rent in advance as security deposit.', 'भाडेतत्वावर देणे आहे: कोरेगाव पार्क येथील अलिशान स्वतंत्र व्हिला कॉर्पोरेट किंवा खाजगी कुटुंबासाठी भाड्याने देणे आहे. अनामत रक्कम: ५ लाख, मासिक भाडे: १.८० लाख.', 1),

('prop-5', 'MA-2026-005', 'pune', 'Premium Penthouse with Rooftop Deck', 'Residential', 'Seller Listed', 'B-Wing, 12th Floor, Trump Towers, Kalyani Nagar, Pune', '₹ 8.20 Cr', 82000000, 'N/A', '₹ 9.50 Cr', 95000000, NULL, 'N/A', 'N/A', 'Physical Possession', 'agt-1', 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80', 'Exquisite builder-floor penthouse directly listed by the owner. Features double-height ceilings, a huge rooftop deck with skyline views, automation controls, and custom marble styling.', 'EXCELLENT DIRECT PRIVATE INVESTMENT SALE: Clear title verified property available for direct purchase from the owner. Price ₹8.20 Cr (market savings of ₹1.3 Cr below government ready reckoner valuation).', 'थेट मालकाकडून विक्री: कायदेशीर मालकी हक्क पडताळणी केलेली मालमत्ता थेट खरेदीसाठी उपलब्ध आहे. किंमत रु. ८.२० कोटी (शासकीय रेडी रेकनर दरापेक्षा १.३ कोटी बचत).', 1),

('prop-6', 'MA-2026-006', 'pune', 'Industrial Shed in Bhosari MIDC', 'Industrial', 'Auction', 'Plot No. J-24, Bhosari MIDC, Pune', '₹ 2.80 Cr', 28000000, '₹ 28.00 Lakhs', '₹ 3.50 Cr', 35000000, '2026-06-25 14:00:00', 'Bank of Baroda', 'Precision Engineering Works', 'Physical', 'agt-3', 'https://images.unsplash.com/photo-1580982327559-c1202864eb05?auto=format&fit=crop&w=800&q=80', 'Heavy-duty industrial manufacturing shed. Features high power load capacity (100 HP), overhead gantry crane tracks (5 Ton capacity), concrete flooring, and clear height of 24 feet.', 'SARFAESI ACT 2002 NOTICE: Secure assets of Precision Engineering Works will be sold online via e-auction for recovery of dues ₹2.45 Cr. Auction scheduled under Section 13(4) of Act.', 'सरफेसी कायदा २००२ नोटीस: प्रिसिजन इंजिनिअरिंग वर्क्सच्या तारण मालमत्तेचा थकबाकी २.४५ कोटी वसुलीसाठी ई-लिलाव. कलम १३(४) अन्वये विक्री जाहीर करण्यात आली आहे.', 1),

('prop-7', 'MA-2026-007', 'nashik', 'Grape Vineyard with Farmhouse in Dindori', 'Agricultural', 'Auction', 'Survey No. 45, Dindori Grape Valley, Nashik', '₹ 3.20 Cr', 32000000, '₹ 32.00 Lakhs', '₹ 4.10 Cr', 41000000, '2026-07-15 10:00:00', 'Central Bank of India', 'Vineyards Estate Pvt Ltd', 'Physical', 'agt-1', 'https://images.unsplash.com/photo-1552594612-9c3f256a4276?auto=format&fit=crop&w=800&q=80', '10-acre fully operational premium grape vineyard in Dindori valley. Includes drip irrigation systems, warehouse storage, water borewells, and a beautiful 2 BHK boutique farmhouse.', 'PUBLIC TENDER AUCTION: Sale under SARFAESI Security Interest Enforcement. 10 acres agricultural grape farm. All assets mortgaged under Central Bank of India will be auctioned online.', 'सार्वजनिक निविदा लिलाव: सरफेसी कायद्यांतर्गत विक्री नोटीस. सेंट्रल बँक ऑफ इंडियाकडे तारण असलेली १० एकर द्राक्ष शेती व फार्महाऊस ऑनलाईन लिलावात विक्रीसाठी उपलब्ध.', 1),

('prop-8', 'MA-2026-008', 'thane', 'Spacious 3 BHK in Hiranandani Estate', 'Residential', 'Auction', 'Tower 4, Hiranandani Estate, Ghodbunder Road, Thane', '₹ 2.10 Cr', 21000000, '₹ 21.00 Lakhs', '₹ 2.70 Cr', 27000000, '2026-07-10 11:00:00', 'Kotak Mahindra Bank', 'Sunil Gavaskar Ltd', 'Physical', 'agt-2', 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=800&q=80', 'High-floor premium luxury residence in Hiranandani Estate, Thane. Exquisite fittings, massive marble tiling, dynamic cross ventilation, and scenic views of the surrounding hills.', 'SALE PROCLAMATION FOR E-AUCTION: Under SARFAESI security enforcement guidelines. Bank dues amount to ₹1.95 Cr. Bids strictly accepted on e-portal until auction day.', 'ई-लिलाव विक्री घोषणापत्र: सरफेसी सुरक्षा कायद्याअंतर्गत नोटीस. कोटक महिंद्रा बँकेचे रु. १.९५ कोटी थकबाकी वसुलीसाठी लिलाव. बोली केवळ अधिकृत वेब पोर्टलवर स्वीकारली जाईल.', 1);
