const fs = require('fs');
const content = fs.readFileSync('public/js/i18n.js', 'utf8');

global.document = {
    addEventListener: (event, cb) => {
        cb();
    }
};

global.localStorage = {
    getItem: () => null,
    setItem: () => null
};

global.Alpine = {
    store: (name, obj) => {
        if (!fs.existsSync('public/locales')) fs.mkdirSync('public/locales');
        fs.writeFileSync('public/locales/en.json', JSON.stringify(obj._en, null, 4));
        fs.writeFileSync('public/locales/es.json', JSON.stringify(obj._es, null, 4));
        console.log('JSON files generated.');
    }
};

eval(content);
