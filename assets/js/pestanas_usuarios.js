function mostrarPestaña(tab) {
    document.getElementById('clientes-tab').classList.remove('active');
    document.getElementById('trabajadores-tab').classList.remove('active');
    document.getElementById('contenido-clientes').style.display = (tab === 'clientes') ? 'block' : 'none';
    document.getElementById('contenido-trabajadores').style.display = (tab === 'trabajadores') ? 'block' : 'none';
    if(tab === 'clientes') {
        document.getElementById('clientes-tab').classList.add('active');
    } else {
        document.getElementById('trabajadores-tab').classList.add('active');
    }
}
window.onload = function() {
    mostrarPestaña('clientes');
};
