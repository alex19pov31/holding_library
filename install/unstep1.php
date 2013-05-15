<?php
if(!check_bitrix_sessid()) return;

echo CAdminMessage::ShowNote(GetMessage("LIBRARY_UNINSTALL_COMPLETE"));
?>