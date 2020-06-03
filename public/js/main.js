window.addEventListener('DOMContentLoaded', () => {
  console.log('Initialising page');
  const auto = new autoComplete({
    selector: '.search > input',
    source: (term, callback) => {
      getSuggestions(term).then(a => callback(a));
    },
    offsetTop: 0,
    minChars: 2,
    delay: 20,
    renderItem: (item, search) => {
      search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
      let re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");

      return `<div class="autocomplete-suggestion" data-aid="${item[1]}" data-val="${search}">${item[0].replace(re, "<b>$1</b>")}</div>`;
    },
    onSelect: (e, term, item) => {
      const animeId = item.dataset.aid;
      location.href = '/torrents/' + animeId + '/' + encodeURI(item.textContent.trim());
    }
  });

  document.querySelectorAll('button.subscribe').forEach((button) => {
    button.addEventListener('click', async () => {
      if (button.classList.contains('subscribed')) {
        const result = await unsubscribe(button.dataset.subscriptionId);
        if (!result) {
          return;
        }

        if (button.parentElement.classList.contains("collection-title"))

        button.classList.remove('subscribed');
        button.textContent = 'Subscribe';
        return;
      }

      const subscriptionId = await subscribe(button.dataset.id, button.dataset.hash);
      if (!subscriptionId) {
        return;
      }

      button.classList.add('subscribed');
      button.dataset.subscriptionId = subscriptionId;
      button.textContent = 'Unsubscribe';
    });
  })
});

async function getSuggestions(term) {
  const resp = await fetch('https://odango.moe/api/title?limit=20&q=' + encodeURIComponent(term));

  const obj = await resp.json();

  return obj.result.map(a => [a.title, a.id]);
}

async function subscribe(id, hash) {
  const resp = await fetch('/api/subscribe', {
    method: 'POST',
    body: JSON.stringify({
      'anime-id': id,
      hash
    })
  });

  if (resp.status !== 200) {
    return false;
  }

  return (await resp.json()).id;
}

async function unsubscribe(id) {
  const resp = await fetch('/api/unsubscribe', {
    method: 'POST',
    body: JSON.stringify({
      id,
    })
  });

  if (resp.status !== 200) {
    return false;
  }

  return true;
}