// Add this right at the top of your script
const GLOBAL_TIMEOUT_MS = 40000; // 60-second limit per screenshot job
setTimeout(() => {
  console.error("CRITICAL ERROR: Script execution timed out globally. Force exiting.");
  process.exit(1);
}, GLOBAL_TIMEOUT_MS).unref(); // .unref() prevents this timeout from keeping the script alive if it finishes normally

const puppeteer = require('puppeteer'); // or playwright

async function capture() {
  let browser = null;
  const url = process.argv[2];
  const outputPath = process.argv[3];

  if (!url || !outputPath) {
    console.error("Usage: node script.js <url> <outputPath>");
    process.exit(1);
  }

  try {
    browser = await puppeteer.launch({
      headless: true,
      //executablePath: '/usr/local/sbin/chrome-devel-sandbox',
      executablePath: '/opt/google/chrome/chrome',
      args: ['--no-sandbox', '--disable-setuid-sandbox'] // Critical flags for Apache / www-data users
    });

    const page = await browser.newPage();
    await page.setViewport({ width: 3840, height: 2160 }); // 4K default config matches your PHP properties


    console.log ('Now loading : '+url);
    await page.evaluateOnNewDocument(() => {
      window.__IS_SCREENSHOT_MODE__ = true;
    });

    // Set waitUntil to 'networkidle2' so it doesn't hang infinitely on trailing tracking scripts
    await page.goto(url, { waitUntil: 'networkidle2', timeout: 30000 });


    // Now wait for YOUR app to say it's ready
    // Timeout after 30 seconds just in case something goes wrong
    await page.waitForFunction(
      'window._screenshotReady === true',
      { timeout: 30000 }
    );

    await page.screenshot({ path: outputPath, fullPage: true });
    console.log("SUCCESS: Screenshot written to " + outputPath);
    process.exit(0);

  } catch (err) {
    console.error("SCREENSHOT FAILURE:", err.message, err.stacktrace);
    process.exit(1);
  } finally {
    if (browser !== null) {
      await browser.close();
    }
  }
}

capture();
