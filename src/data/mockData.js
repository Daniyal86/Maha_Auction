export const cities = [
  { id: 'mumbai', name: 'Mumbai', propertyCount: 425, coordinates: { x: 18, y: 55 } },
  { id: 'thane', name: 'Thane', propertyCount: 312, coordinates: { x: 22, y: 48 } },
  { id: 'pune', name: 'Pune', propertyCount: 380, coordinates: { x: 26, y: 68 } },
  { id: 'nashik', name: 'Nashik', propertyCount: 145, coordinates: { x: 28, y: 35 } },
  { id: 'aurangabad', name: 'Aurangabad', propertyCount: 92, coordinates: { x: 45, y: 45 } },
  { id: 'nagpur', name: 'Nagpur', propertyCount: 189, coordinates: { x: 82, y: 35 } },
];

export const properties = [
  // MUMBAI PROPERTIES
  {
    id: 'prop-1', cityId: 'mumbai', title: '3 BHK Sea-facing Apartment in Worli', type: 'Residential',
    address: 'A-Wing, 14th Floor, Sea View Heights, Worli', reservePrice: '₹ 4.50 Cr', emd: '₹ 45.00 Lakhs',
    auctionDate: '2026-06-15T11:00:00Z', bank: 'State Bank of India', borrower: 'Rajesh Sharma', possession: 'Physical',
    image: 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80',
    details: 'Spacious 3 BHK apartment with unobstructed sea views. Building features premium amenities including pool, gym, and 24/7 security.'
  },
  {
    id: 'prop-1b', cityId: 'mumbai', title: 'Luxury 4 BHK Penthouse in Bandra West', type: 'Residential',
    address: 'Carter Road, Bandra West', reservePrice: '₹ 15.00 Cr', emd: '₹ 1.50 Cr',
    auctionDate: '2026-06-22T10:00:00Z', bank: 'HDFC Bank', borrower: 'Mehra Constructions', possession: 'Physical',
    image: 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80',
    details: 'Fully furnished penthouse with private terrace and infinity pool. Walking distance to prominent cafes.'
  },
  {
    id: 'prop-2', cityId: 'mumbai', title: 'Commercial Office Space in BKC', type: 'Commercial',
    address: 'Unit 402, 4th Floor, Platinum Tower, BKC', reservePrice: '₹ 12.00 Cr', emd: '₹ 1.20 Cr',
    auctionDate: '2026-06-20T10:30:00Z', bank: 'ICICI Bank', borrower: 'TechVision Solutions', possession: 'Symbolic',
    image: 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=800&q=80',
    details: 'Prime commercial office space. Fully furnished with 50 workstations and 2 conference rooms.'
  },
  {
    id: 'prop-2b', cityId: 'mumbai', title: 'Retail Shop in Andheri West', type: 'Commercial',
    address: 'Ground Floor, Link Road, Andheri West', reservePrice: '₹ 3.50 Cr', emd: '₹ 35.00 Lakhs',
    auctionDate: '2026-07-01T12:00:00Z', bank: 'Bank of Baroda', borrower: 'Krupa Retail Pvt Ltd', possession: 'Physical',
    image: 'https://images.unsplash.com/photo-1555529771-835f59fc5efe?auto=format&fit=crop&w=800&q=80',
    details: 'High footfall retail shop facing the main road. Ideal for clothing brands or electronics.'
  },

  // PUNE PROPERTIES
  {
    id: 'prop-3', cityId: 'pune', title: '4 BHK Luxury Villa in Koregaon Park', type: 'Residential',
    address: 'Villa No. 7, Silver Oaks Society, Koregaon Park', reservePrice: '₹ 6.25 Cr', emd: '₹ 62.50 Lakhs',
    auctionDate: '2026-06-18T12:00:00Z', bank: 'ICICI Bank', borrower: 'Amit Deshmukh', possession: 'Physical',
    image: 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80',
    details: 'Independent luxury villa with private garden, swimming pool, and premium imported fittings.'
  },
  {
    id: 'prop-3b', cityId: 'pune', title: '2 BHK Modern Flat in Hinjewadi Phase 1', type: 'Residential',
    address: 'B-205, Tech Park Heights, Hinjewadi Phase 1', reservePrice: '₹ 65.00 Lakhs', emd: '₹ 6.50 Lakhs',
    auctionDate: '2026-06-25T11:00:00Z', bank: 'Union Bank of India', borrower: 'Rahul Verma', possession: 'Symbolic',
    image: 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80',
    details: 'Perfect for IT professionals. Just 5 minutes walk from major tech parks. Well maintained society.'
  },
  {
    id: 'prop-4', cityId: 'pune', title: 'Industrial Shed in Bhosari MIDC', type: 'Industrial',
    address: 'Plot No. J-24, Bhosari MIDC, Pune', reservePrice: '₹ 2.80 Cr', emd: '₹ 28.00 Lakhs',
    auctionDate: '2026-06-25T14:00:00Z', bank: 'Bank of Baroda', borrower: 'Precision Engineering Works', possession: 'Physical',
    image: 'https://images.unsplash.com/photo-1580982327559-c1202864eb05?auto=format&fit=crop&w=800&q=80',
    details: 'Well-maintained industrial shed with heavy duty flooring, 100 HP power connection.'
  },

  // THANE PROPERTIES
  {
    id: 'prop-t1', cityId: 'thane', title: 'Spacious 3 BHK in Hiranandani Estate', type: 'Residential',
    address: 'Tower 4, Hiranandani Estate, Ghodbunder Road', reservePrice: '₹ 2.10 Cr', emd: '₹ 21.00 Lakhs',
    auctionDate: '2026-07-10T11:00:00Z', bank: 'Kotak Mahindra Bank', borrower: 'Sunil Gavaskar', possession: 'Physical',
    image: 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=800&q=80',
    details: 'Premium township living. Includes massive clubhouse access and uninterrupted mountain views.'
  },

  // NAGPUR PROPERTIES
  {
    id: 'prop-5', cityId: 'nagpur', title: '2 BHK Apartment in Dharampeth', type: 'Residential',
    address: 'Flat 302, Gokul Dham, Dharampeth', reservePrice: '₹ 85.00 Lakhs', emd: '₹ 8.50 Lakhs',
    auctionDate: '2026-06-10T11:00:00Z', bank: 'Punjab National Bank', borrower: 'Sanjay Patil', possession: 'Physical',
    image: 'https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=800&q=80',
    details: 'Centrally located 2 BHK flat near markets and schools. Includes one covered parking space.'
  },
  {
    id: 'prop-5b', cityId: 'nagpur', title: 'Large Commercial Plot on Wardha Road', type: 'Commercial',
    address: 'Plot 45, Main Highway, Wardha Road', reservePrice: '₹ 5.50 Cr', emd: '₹ 55.00 Lakhs',
    auctionDate: '2026-07-02T13:00:00Z', bank: 'State Bank of India', borrower: 'Logistics India Ltd', possession: 'Physical',
    image: 'https://images.unsplash.com/photo-1531834685032-c34bf0d84c77?auto=format&fit=crop&w=800&q=80',
    details: 'Excellent highway frontage. Suitable for a large showroom or warehousing facility.'
  },

  // NASHIK PROPERTIES
  {
    id: 'prop-6', cityId: 'nashik', title: 'Agricultural Land in Niphad', type: 'Agricultural',
    address: 'Gat No. 112, Village Ojhar, Taluka Niphad', reservePrice: '₹ 1.50 Cr', emd: '₹ 15.00 Lakhs',
    auctionDate: '2026-07-05T15:00:00Z', bank: 'Bank of Maharashtra', borrower: 'Kisan Agro Industries', possession: 'Symbolic',
    image: 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=800&q=80',
    details: 'Fertile agricultural land measuring 5 acres with direct access to canal water and a small farm house.'
  },
  {
    id: 'prop-6b', cityId: 'nashik', title: 'Grape Vineyard with Farmhouse', type: 'Agricultural',
    address: 'Survey No 45, Dindori Road, Nashik', reservePrice: '₹ 3.20 Cr', emd: '₹ 32.00 Lakhs',
    auctionDate: '2026-07-15T10:00:00Z', bank: 'Central Bank of India', borrower: 'Vineyards Estate Pvt Ltd', possession: 'Physical',
    image: 'https://images.unsplash.com/photo-1552594612-9c3f256a4276?auto=format&fit=crop&w=800&q=80',
    details: '10-acre active vineyard. Includes drip irrigation setup, storage sheds, and a 2 BHK farmhouse.'
  },

  // AURANGABAD PROPERTIES
  {
    id: 'prop-a1', cityId: 'aurangabad', title: 'Industrial Plot in Waluj MIDC', type: 'Industrial',
    address: 'Plot K-12, Sector 2, Waluj MIDC', reservePrice: '₹ 1.90 Cr', emd: '₹ 19.00 Lakhs',
    auctionDate: '2026-07-20T11:00:00Z', bank: 'Bank of India', borrower: 'Auto Parts Manufacturing Ltd', possession: 'Physical',
    image: 'https://images.unsplash.com/photo-1587293852726-694b60279e34?auto=format&fit=crop&w=800&q=80',
    details: 'Open industrial plot with boundary walls and basic infrastructure ready for construction.'
  }
];
