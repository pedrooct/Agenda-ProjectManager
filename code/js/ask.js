function closeWin() {
	alert("Verifique o seu email para mais informações! Esta página vai se fechar...");
	//window.open('','_parent','');
	setTimeout("window.close()", 500);
}
function OpenWin(link) {
	location.assign(link);
}
