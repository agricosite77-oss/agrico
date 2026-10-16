import fs from 'fs';

const data = JSON.parse(fs.readFileSync('tests/responsive/responsive_audit_report.json', 'utf8'));
const problems = data.filter(d => d.result === 'Problem');
console.log('Total audited configurations:', data.length);
console.log('Total problems found:', problems.length);
console.log('--- PROBLEMS ---');
for (const p of problems) {
  console.log(`- [${p.page}] [${p.device}] ${p.dimensions}: ${p.issueSummary} | Screenshot: ${p.screenshot}`);
}

const tableChecks = data.filter(d => d.tables && d.tables.length > 0);
console.log('\n--- PAGES WITH DATA TABLES ---');
const uniqueTablePages = [...new Set(tableChecks.map(d => d.page))];
for (const tp of uniqueTablePages) {
  const ex = tableChecks.find(d => d.page === tp);
  console.log(`- ${tp}: ${ex.tables.length} table(s) detected. Has scroll container: ${ex.tables.map(t => t.hasScrollContainer).join(', ')}`);
}

console.log('\n--- TAP TARGET CHECKS (iPhone 13 mini: 375x812) ---');
const iphoneTests = data.filter(d => d.dimensions === '375x812');
for (const it of iphoneTests) {
  if (it.smallTargetsCount > 0) {
    console.log(`- ${it.page}: ${it.smallTargetsCount} elements under 40x40px`);
  }
}

