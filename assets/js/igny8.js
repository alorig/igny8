// 🚀 Wait for the full DOM to load before running any Igny8 logic
document.addEventListener("DOMContentLoaded", function () {
  // 🎯 Step 1: Get the Igny8 trigger button
  const trigger = document.getElementById("igny8-launch");
  if (!trigger) return; // Exit if trigger is not present on the page

  // 🎬 Step 2: Handle button click to start personalization
  trigger.addEventListener("click", function () {
    // 🧱 Step 3: Show initial loading message
    const output = document.getElementById("igny8-output");
    output.innerHTML = "<div>⏳ Loading personalization form...</div>";

    // 📋 Step 4: Get the list of field IDs to render (e.g., "4,5,6")
    const formFields = trigger.dataset.formFields || '';

    // 🏗️ Step 5: Build query string parameters for AJAX request
    const params = new URLSearchParams();
    params.append('action', 'igny8_get_fields');
    params.append('post_id', trigger.dataset.postId);
    params.append('form_fields', formFields);

    // ➕ Step 5b: Append all other context values from data-* attributes
    Object.entries(trigger.dataset).forEach(([key, val]) => {
      if (!['ajaxUrl', 'postId', 'formFields'].includes(key)) {
        params.append(key, val);
      }
    });

    // 🌐 Step 6: Make AJAX call to load dynamic form HTML
    fetch(`${trigger.dataset.ajaxUrl}?${params.toString()}`)
      .then(res => res.text())
      .then(html => {
        // 🎨 Step 7: Inject form into the output div
        output.innerHTML = html;

const contextEl = document.getElementById("igny8-context");
const pageContentField = document.querySelector("#igny8-form [name='PageContent']");

if (contextEl && pageContentField) {
  // ✅ Clone context DOM to safely manipulate without touching original
  const clone = contextEl.cloneNode(true);

  // ✅ Replace all <br> tags with actual newlines
  clone.querySelectorAll('br').forEach(br => br.replaceWith('\n'));

  const cleanedText = clone.textContent
    .replace(/\n{2,}/g, '\n')     // Collapse multiple newlines
    .replace(/[ \t]+\n/g, '\n')   // Remove trailing space/tab before newlines
    .trim();

  pageContentField.value = `[SOURCE:JS-injected PageContext]\n\n` + cleanedText;
  console.log("✅ Injected PageContent:\n", pageContentField.value);
}





        // 🧼 Step 7b: Hide original teaser + button
        trigger.closest("#igny8-trigger").style.display = "none";

        // 📝 Step 8: Bind form submission logic (only once)
        const form = document.getElementById("igny8-form");
        if (form && !form.dataset.bound) {
          form.dataset.bound = "true";

          form.addEventListener("submit", function (e) {
            e.preventDefault();

            // 🔍 Step 8.1: Fill PageContent with context from admin-defined shortcode
            const contextEl = document.getElementById('igny8-context');
            const pageContentField = form.querySelector('[name="PageContent"]');
            if (contextEl && pageContentField && !pageContentField.value.trim()) {
              pageContentField.value = contextEl.textContent.trim();
            }

            // 🧾 Step 9: Gather form inputs for GPT
            const formData = new FormData(form);
            const resultBox = document.getElementById("igny8-generated-content");

            // ⏳ Step 9b: Show loading spinner while GPT responds
            resultBox.innerHTML = `
              <div class='igny8-loading'>
                <div class='igny8-spinner'></div>
                <span>Personalizing...</span>
              </div>
            `;

            // 🐞 Step 9c: Log form data in console for debugging
            console.log("🔄 Submitting Igny8 form with data:", Object.fromEntries(formData.entries()));

            // 🤖 Step 10: Send form data to backend for OpenAI processing
            fetch(trigger.dataset.ajaxUrl + "?action=igny8_generate_custom&post_id=" + trigger.dataset.postId, {
              method: "POST",
              body: formData,
            })
              .then(res => res.text())
              .then(html => {
                resultBox.innerHTML = html; // ✅ Show GPT output
              })
              .catch(err => {
                // ❌ Step 11: Handle backend/GPT errors
                resultBox.innerHTML = "<div style='color:red;'>⚠️ Failed to personalize content.</div>";
                console.error("Igny8 generation error:", err);
              });
          });
        }
      })
      .catch(err => {
        // ❌ Step 12: Handle form loading errors (e.g. network/API issues)
        output.innerHTML = "<div style='color:red;'>⚠️ Failed to load Igny8 form.</div>";
        console.error("Igny8 form load error:", err);
      });
  });
});
