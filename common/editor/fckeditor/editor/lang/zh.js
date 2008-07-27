var FCKLang =
{
// Language direction : "ltr" (left to right) or "rtl" (right to left).
Dir					: "ltr",

// Toolbar Items and Context Menu
Preview				: "預覽",
Cut					: "剪下",
Copy				: "複製",
Paste				: "貼上",
PasteText			: "貼為純文字格式",
PasteWord			: "自 Word 貼上",
SelectAll			: "全選",
RemoveFormat		: "清除格式",
InsertLinkLbl		: "超連結",
InsertLink			: "插入/編輯超連結",
RemoveLink			: "移除超連結",
Anchor				: "插入/編輯錨點",
AnchorDelete		: "移除錨點",
InsertImageLbl		: "影像",
InsertImage			: "插入/編輯影像",
InsertFlashLbl		: "Flash",
InsertFlash			: "插入/編輯 Flash",
InsertTableLbl		: "表格",
InsertTable			: "插入/編輯表格",
InsertLineLbl		: "水平線",
InsertLine			: "插入水平線",
InsertSmileyLbl		: "表情符號",
InsertSmiley		: "插入表情符號",
About				: "關於 FCKeditor",
Bold				: "粗體",
Italic				: "斜體",
Underline			: "底線",
StrikeThrough		: "刪除線",
Subscript			: "下標",
Superscript			: "上標",
LeftJustify			: "靠左對齊",
CenterJustify		: "置中",
RightJustify		: "靠右對齊",
BlockJustify		: "左右對齊",
DecreaseIndent		: "減少縮排",
IncreaseIndent		: "增加縮排",
Blockquote			: "块引用",
Undo				: "復原",
Redo				: "重複",
NumberedListLbl		: "編號清單",
NumberedList		: "插入/移除編號清單",
BulletedListLbl		: "項目清單",
BulletedList		: "插入/移除項目清單",
ShowTableBorders	: "顯示表格邊框",
ShowDetails			: "顯示詳細資料",
Style				: "樣式",
FontFormat			: "格式",
Font				: "字體",
FontSize			: "大小",
TextColor			: "文字顏色",
BGColor				: "背景顏色",
Source				: "原始碼",
Find				: "尋找",
Replace				: "取代",
UniversalKeyboard	: "萬國鍵盤",
PageBreakLbl		: "分頁符號",
PageBreak			: "插入分頁符號",
InsertCodes			: "插入代碼",

FontFormats			: "一般;已格式化;位址;標題 1;標題 2;標題 3;標題 4;標題 5;標題 6;一般 (DIV)",

// Alerts and Messages
ProcessingXHTML		: "處理 XHTML 中，請稍候…",
Done				: "完成",
PasteWordConfirm	: "您想貼上的文字似乎是自 Word 複製而來，請問您是否要先清除 Word 的格式後再行貼上？",
NotCompatiblePaste	: "此指令僅在 Internet Explorer 5.5 或以上的版本有效。請問您是否同意不清除格式即貼上？",
UnknownToolbarItem	: "未知工具列項目 \"%1\"",
UnknownCommand		: "未知指令名稱 \"%1\"",
NotImplemented		: "尚未安裝此指令",
UnknownToolbarSet	: "工具列設定 \"%1\" 不存在",
NoActiveX			: "瀏覽器的安全性設定限制了本編輯器的某些功能。您必須啟用安全性設定中的「執行ActiveX控制項與外掛程式」項目，否則本編輯器將會出現錯誤並缺少某些功能",
DialogBlocked		: "無法開啟對話視窗，請確定所有快顯視窗封鎖程式是否關閉",

// Dialogs
DlgBtnOK			: "確定",
DlgBtnCancel		: "取消",
DlgBtnClose			: "關閉",
DlgOpOther			: "<其他>",
DlgInfoTab			: "資訊",
DlgAlertUrl			: "請插入 URL",

// General Dialogs Labels
DlgGenNotSet		: "<尚未設定>",

// Image Dialog
DlgImgTitle			: "影像屬性",
DlgImgInfoTab		: "影像資訊",
DlgImgURL			: "URL",
DlgImgAlt			: "替代文字",
DlgImgWidth			: "寬度",
DlgImgHeight		: "高度",
DlgImgBorder		: "邊框",
DlgImgHSpace		: "水平距離",
DlgImgVSpace		: "垂直距離",
DlgImgAlign			: "對齊",
DlgImgAlignLeft		: "靠左對齊",
DlgImgAlignAbsBottom: "絕對下方",
DlgImgAlignAbsMiddle: "絕對中間",
DlgImgAlignBaseline	: "基準線",
DlgImgAlignBottom	: "靠下對齊",
DlgImgAlignMiddle	: "置中對齊",
DlgImgAlignRight	: "靠右對齊",
DlgImgAlignTextTop	: "文字上方",
DlgImgAlignTop		: "靠上對齊",
DlgImgAlertUrl		: "請輸入影像 URL",
DlgImgLinkTab		: "超連結",

// Flash Dialog
DlgFlashTitle		: "Flash 屬性",
DlgFlashChkPlay		: "自動播放",
DlgFlashChkLoop		: "重複",
DlgFlashChkMenu		: "開啟選單",
DlgFlashScale		: "縮放",
DlgFlashScaleAll	: "全部顯示",
DlgFlashScaleNoBorder	: "無邊框",
DlgFlashScaleFit	: "精確符合",

// Code Dialog
DlgCodesTitle		: "插入代碼",
DlgCodesLanguage	: "語言",
DlgCodesContent		: "內容",

// Link Dialog
DlgLnkWindowTitle	: "超連結",
DlgLnkInfoTab		: "超連結資訊",
DlgLnkTargetTab		: "目標",

DlgLnkType			: "超連接類型",
DlgLnkTypeURL		: "URL",
DlgLnkURL			: "URL",
DlgLnkAnchorSel		: "請選擇錨點",
DlgLnkAnchorByName	: "依錨點名稱",
DlgLnkAnchorById	: "依元件 ID",
DlgLnkNoAnchors		: "(本文件尚無可用之錨點)",

DlgLnkTarget		: "目標",
DlgLnkTargetFrame	: "<框架>",
DlgLnkTargetPopup	: "<快顯視窗>",
DlgLnkTargetBlank	: "新視窗 (_blank)",
DlgLnkTargetParent	: "父視窗 (_parent)",
DlgLnkTargetSelf	: "本視窗 (_self)",
DlgLnkTargetTop		: "最上層視窗 (_top)",
DlgLnkTargetFrameName	: "目標框架名稱",

DlnLnkMsgNoUrl		: "請輸入欲連結的 URL",
DlnLnkMsgNoAnchor	: "請選擇錨點",
DlnLnkMsgInvPopName	: "快顯名稱必須以「英文字母」為開頭，且不得含有空白",

// Color Dialog
DlgColorTitle		: "請選擇顏色",
DlgColorBtnClear	: "清除",
DlgColorHighlight	: "預覽",
DlgColorSelected	: "選擇",

// Smiley Dialog
DlgSmileyTitle		: "插入表情符號",

// Table Dialog
DlgTableTitle		: "表格屬性",
DlgTableRows		: "列數",
DlgTableColumns		: "欄數",
DlgTableBorder		: "邊框",
DlgTableAlign		: "對齊",
DlgTableAlignNotSet	: "<未設定>",
DlgTableAlignLeft	: "靠左對齊",
DlgTableAlignCenter	: "置中",
DlgTableAlignRight	: "靠右對齊",
DlgTableWidth		: "寬度",
DlgTableWidthPx		: "像素",
DlgTableWidthPc		: "百分比",
DlgTableHeight		: "高度",
DlgTableCellSpace	: "間距",
DlgTableCellPad		: "內距",
DlgTableCaption		: "標題",
DlgTableSummary		: "摘要",

// Find and Replace Dialog
DlgFindAndReplaceTitle	: "尋找與取代",

// Find Dialog
DlgFindTitle		: "尋找",
DlgFindFindBtn		: "尋找",
DlgFindNotFoundMsg	: "未找到指定的文字。",

// Replace Dialog
DlgReplaceTitle			: "取代",
DlgReplaceFindLbl		: "尋找:",
DlgReplaceReplaceLbl	: "取代:",
DlgReplaceCaseChk		: "大小寫須相符",
DlgReplaceReplaceBtn	: "取代",
DlgReplaceReplAllBtn	: "全部取代",
DlgReplaceWordChk		: "全字相符",

// Paste Operations / Dialog
PasteErrorCut	: "瀏覽器的安全性設定不允許編輯器自動執行剪下動作。請使用快捷鍵 (Ctrl+X) 剪下。",
PasteErrorCopy	: "瀏覽器的安全性設定不允許編輯器自動執行複製動作。請使用快捷鍵 (Ctrl+C) 複製。",

PasteAsText		: "貼為純文字格式",
PasteFromWord	: "自 Word 貼上",

DlgPasteMsg2	: "請使用快捷鍵 (<strong>Ctrl+V</strong>) 貼到下方區域中並按下 <strong>確定</strong>",
DlgPasteSec		: "因為瀏覽器的安全性設定，本編輯器無法直接存取您的剪貼簿資料，請您自行在本視窗進行貼上動作。",
DlgPasteIgnoreFont		: "移除字型設定",
DlgPasteRemoveStyles	: "移除樣式設定",

// Color Picker
ColorAutomatic	: "自動",

// Anchor Dialog
DlgAnchorTitle		: "命名錨點",
DlgAnchorName		: "錨點名稱",
DlgAnchorErrorName	: "請輸入錨點名稱",

// About Dialog
DlgAboutAboutTab	: "關於",
DlgAboutVersion		: "版本",
DlgAboutInfo		: "想獲得更多資訊請至 "
};
