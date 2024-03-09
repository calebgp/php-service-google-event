<form method="POST">
    <label for="summary">Summary: </label>
    <input type="text" id="summary" name="summary" value="Consulta Médica"/><br/>
    <label for="description" style="align-self: revert">Description: </label>
    <textarea id="description" name="description">Minha consulta</textarea><br/>
    <label for="starts">Start:</label>
    <input id="starts" type="datetime-local" name="starts"/><br/>
    <label for="ends">End:</label>
    <input id="ends" type="datetime-local" name="ends"/><br/>
    <label for="doctor">Doctor:</label>
    <input id="doctor" type="email" name="doctor" value="raphaelcpinto@gmail.com"/><br/>
    <label for="patient">Patient:</label>
    <input id="patient" type="email" name="patient" value="calebgp.pers@gmail.com"/><br/><br/>
    <button type="submit">Enviar</button>
    <button type="reset">Limpar</button>
</form>