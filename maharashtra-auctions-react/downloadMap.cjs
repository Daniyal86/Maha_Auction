const https = require('https');
const fs = require('fs');

const url = 'https://d-maps.com/m/asia/india/maharashtra/maharashtra14.gif';
const file = fs.createWriteStream('./public/maharashtra-map.gif');

https.get(url, { headers: { 'User-Agent': 'Mozilla/5.0' } }, (response) => {
  if (response.statusCode === 200) {
    response.pipe(file);
    file.on('finish', () => {
      file.close();
      console.log('Map downloaded successfully.');
    });
  } else {
    console.error(`Failed to download map. Status code: ${response.statusCode}`);
  }
}).on('error', (err) => {
  console.error(`Error: ${err.message}`);
});
