{include file='header.tpl'}

<h3>Dear <b>{$owner}</b></h3>
<p>You've subscribed cutting edge judging and scoring system <b>OPTIMUS</b>.<br>
This is a resend of your product activation information. </p>
<ul>
  <li>License type: {$type}</li>
  <li>valid period: {$datestart } to {$dateend} </li>
  <li>Owner: {$owner} </li>
  <li>Included disciplices:
    <ul>
      {foreach $desc as $i} <li>{$i}</li> {/foreach}
    </ul>
  </li>
</ul>
<p>If you haven’t already downloaded the software or need to download again, visit to <a href="http://127.0.0.1/download.html">download page</a>.</p>
<p>You can find the license file in attachment. <br>
IMPORTANT! Please keep it carefully, Do not share the license file with third parties since It is used as an access key for publishing data on the WEB.</p>
<p>Useful links to help you get started:
  <ul>
    <li>Как подключить лицензию см. <a href="DOC/Методчка.doc"> раздел "Запуск программы"</a>методички.</li>
    <li>Остались вопросы?  Свяжитесь с <a href="mailto:support@optimus.dp.ua">нашей поддержкой</a>.</li>
  </ul>
</p>

{include file='footer.tpl'}

