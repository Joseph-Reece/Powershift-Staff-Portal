<script>
    function clearSelect(elId){
        el = document.getElementById(elId);
        while (el.options.length > 0) {
            el.remove(0);
        }
        var elOption = document.createElement("option");
        elOption.textContent = '--select--';
        elOption.value = '';
        el.appendChild(elOption);
    }
</script>
