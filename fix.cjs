const fs = require('fs');

['public/locales/en.json', 'public/locales/es.json'].forEach(file => {
    let content = fs.readFileSync(file, 'utf8');

    // Fix unclosed objects
    content = content.replace(/"calendar": \{\s*"view": "([^"]*)",/g, '"calendar": {\n    "view": "$1"\n  },');
    content = content.replace(/"list": \{\s*"view": "([^"]*)",/g, '"list": {\n    "view": "$1"\n  },');
    content = content.replace(/"label": \{\s*"income":/g, '"label": {\n    "income":');
    content = content.replace(/"scheduledAppointments",\s*"menu": \{/g, '"scheduledAppointments"\n  },\n  "menu": {');
    content = content.replace(/"menu": \{\s*"inventory": "([^"]*)",/g, '"menu": {\n    "inventory": "$1"\n  },');

    fs.writeFileSync(file, content);

    try { 
        JSON.parse(content); 
        console.log(file + ' valid!'); 
    } catch(e) { 
        console.log(file + ' still invalid: ' + e.message); 
    }
});
