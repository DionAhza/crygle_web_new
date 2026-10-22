import Plyr from 'plyr';
import 'plyr/dist/plyr.css';

import hljs from 'highlight.js/lib/core';
import 'highlight.js/styles/github.css';
import php from 'highlight.js/lib/languages/php';
import javascript from 'highlight.js/lib/languages/javascript';
import xml from 'highlight.js/lib/languages/xml';
import css from 'highlight.js/lib/languages/css';
import sql from 'highlight.js/lib/languages/sql';
import bash from 'highlight.js/lib/languages/bash';
import json from 'highlight.js/lib/languages/json';
import python from 'highlight.js/lib/languages/python';

hljs.registerLanguage('php', php);
hljs.registerLanguage('javascript', javascript);
hljs.registerLanguage('js', javascript);
hljs.registerLanguage('xml', xml);
hljs.registerLanguage('html', xml);
hljs.registerLanguage('css', css);
hljs.registerLanguage('sql', sql);
hljs.registerLanguage('bash', bash);
hljs.registerLanguage('sh', bash);
hljs.registerLanguage('json', json);
hljs.registerLanguage('python', python);

document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('lesson-player');
    if (el) {
        new Plyr(el, { youtube: { noCookie: true, rel: 0 } });
    }

    document.querySelectorAll('.lesson-prose pre code').forEach((block) => hljs.highlightElement(block));
});
