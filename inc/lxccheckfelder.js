
//window.myalert=function(){var D=2;var F={button_title:"ok",left:-1,top:-1,width:-1,height:-1};if(arguments.length==2&&typeof arguments[1]=="object"){F=E(arguments[1],F)}else{if(arguments.length==3){F=E(arguments[2],F)}}var H=document.getElementById("alert");if(H){document.body.removeChild(H)}H=document.createElement("DIV");H.id=H.className="alert";document.body.appendChild(H);if(arguments.length==1||(arguments.length==2&&typeof arguments[1]!="string")){arguments=["",arguments[0]]}H.innerHTML=(arguments[0]==""?"":'<div class="title">'+arguments[0]+"</div>")+'<div class="body">'+arguments[1]+'</div><div class="button"><a href="" onclick="document.body.removeChild(document.getElementById(\'alert\'));return false;">'+F.button_title+"</a></div>";var G=A(),C=Math.max(B(arguments[0]),B(arguments[1]))*6;if(F.width==-1){F.width=C}if(F.left==-1){F.left=parseInt((G[0]+G[2]-C)/2)}if(F.top==-1){F.top=parseInt((G[1]+G[3]-(H.offsetHeight||H.pixelHeight))/2)}H.style.width=F.width+"px";if(F.height>0){H.style.height=F.height+"px"}H.style.left=F.left+"px";H.style.top=F.top+"px";H.style.display="block";function E(J,I){for(var K in I){if(K in J){continue}J[K]=I[K]}return J}function B(L){var M=L.split("<br />");if(M.length<=1){M=L.split("<br>")}if(M.length<=1){return L.replace(/<(?:.|\s)*?>/g,"").length}var I=0;for(var K=0;K<M.length;K++){var J=M[K].replace(/<(?:.|\s)*?>/g,"");if(J.length>I){I=J.length}}return I}function A(){var J=0,K=0,I=0,L=0;if(typeof window.innerWidth=="number"){J=window.innerWidth;K=window.innerHeight}else{if(document.documentElement&&(document.documentElement.clientWidth||document.documentElement.clientHeight)){J=document.documentElement.clientWidth;K=document.documentElement.clientHeight}else{if(document.body&&(document.body.clientWidth||document.body.clientHeight)){J=document.body.clientWidth;K=document.body.clientHeight}}}if(typeof window.pageYOffset=="number"){L=window.pageYOffset;I=window.pageXOffset}else{if(document.body&&(document.body.scrollLeft||document.body.scrollTop)){L=document.body.scrollTop;I=document.body.scrollLeft}else{if(document.documentElement&&(document.documentElement.scrollLeft||document.documentElement.scrollTop)){L=document.documentElement.scrollTop;I=document.documentElement.scrollLeft}}}return[I,L,J,K]}};
function valid_kz(kz){
        var de_kz = ",A,AA,AB,ABG,AC,AE,AIC,AK,AM,AN,ANA,ANG,ANK,AÖ,AP,APD,ARN,ART,AS,ASL,ASZ,AT,AU,AUR,AW,AZ,AZE,B,BA,BAD,BAR,BB,BBG,BBL,BC,BD,BED,BEL,BER,BGL,BI,BIR,BIT,BIW,BL,BLK,BM,BN,BNA,BO,BÖ,BOR,BOT,BP,BRA,BRB,BRG,BS,BSK,BT,BTF,BÜS,BÜZ,BW,BWL,BYL,BZ,C,CA,CB,CE,CHA,CLP,CO,COC,COE,CUX,CW,D,DA,DAH,DAN,DAU,DB,DBR,DD,DE,DEG,DEL,DGF,DH,DL,DLG,DM,DN,DO,DON,DS,DU,DÜW,DW,DZ,E,EA,EB,EBE,ED,EE,EF,EH,EI,EIC,EIL,EIS,EL,EM,EMD,EMS,EN,ER,ERB,ERH,ES,ESA,ESW,EU,EW,F,FB,FD,FDS,FF,FFB,FG,FI,FL,FLÖ,FN,FO,FOR,FR,FRG,FRI,FRW,FS,FT,FTL,FÜ,FW,G,GA,GAP,GC,GDB,GE,GER,GF,GG,GHA,GHC,GI,GL,GM,GMN,GNT,GÖ,GP,GR,GRH,GRM,GRS,GRZ,GS,GT,GTH,GÜ,GUB,GVM,GW,GZ,H,HA,HAL,HAM,HAS,HB,HBN,HBS,HC,HD,HDH,HDL,HE,HEF,HEI,HEL,HER,HET,HF,HG,HGN,HGW,HH,HHM,HI,HIG,HL,HM,HN,HO,HOL,HOM,HOT,HP,HR,HRO,HS,HSK,HST,HU,HV,HVL,HWI,HX,HY,HZ,IGB,IK,IL,IN,IZ,J,JB,JE,JL,K,KA,KB,KC,KE,KEH,KF,KG,KH,KI,KIB,KL,KLE,KLZ,KM,KN,KO,KÖT,KR,KS,KT,KU,KÜN,KUS,KW,KY,KYF,L,LA,LAU,LB,LBS,LBZ,LC,LD,LDK,LDS,LER,LEV,LG,LI,LIB,LIF,LIP,LL,LM,LN,LÖ,LÖB,LOS,LSA,LSN,LSZ,LU,LUK,LWL,M,MA,MAB,MB,MC,MD,ME,MEI,MEK,MER,MG,MGN,MH,MHL,MI,MIL,MK,MKK,ML,MM,MN,MOL,MOS,MQ,MR,MS,MSP,MST,MTK,MÜ,MÜR,MVL,MW,MYK,MZ,MZG,N,NAU,NB,ND,NDH,NE,NEA,NEB,NES,NEW,NF,NH,NI,NK,NL,NM,NMB,NMS,NOH,NOL,NOM,NP,NR,NRW,NU,NVP,NW,NWM,NY,NZ,OA,OAL,OB,OBG,OC,OD,OE,OF,OG,OH,OHA,OHV,OHZ,OK,OL,OPR,OR,OS,OSL,OVL,OVP,OZ,P,PA,PAF,PAN,PB,PCH,PE,PER,PF,PI,PIR,PK,PL,PLÖ,PM,PN,PR,PS,PW,PZ,QFT,QLB,R,RA,RC,RD,RDG,RE,REG,RG,RH,RIE,RL,RM,RN,RO,ROS,ROW,RP,RPL,RS,RSL,RT,RU,RÜD,RÜG,RV,RW,RZ,S,SAD,SAL,SAW,SB,SBG,SBK,SC,SCZ,SDH,SDL,SDT,SE,SEB,SEE,SFA,SFB,SFT,SG,SGH,SH,SHA,SHG,SHK,SHL,SI,SIG,SIM,SK,SL,SLF,SLN,SLS,SLZ,SM,SN,SO,SOK,SÖM,SON,SP,SPB,SPN,SR,SRB,SRO,ST,STA,STB,STD,STL,SU,SÜW,SW,SZ,SZB,TBB,TET,TF,TG,THL,THW,TIR,TO,TÖL,TP,TR,TS,TÜ,TUT,UE,UEM,UER,UH,UL,UM,UN,V,VB,VEC,VER,VIE,VK,VS,W,WAF,WAK,WB,WBS,WDA,WE,WEN,WES,WF,WHV,WI,WIL,WIS,WK,WL,WLG,WM,WMS,WN,WND,WO,WOB,WR,WRN,WSF,WST,WSW,WT,WTM,WÜ,WUG,WUN,WUR,WW,WZL,X,Y,Z,ZE,ZI,ZP,ZR,ZS,ZW,ZZ,";
        var kz = kz.split("-");
        var suche = "," + kz[0] + ",";
        var erg = de_kz.search(suche);
        return erg;

        }

function kz_to_lks(kz){
    $( "#dialog" ).dialog({
        modal: false,
        title: 'Kenzeichen Info'
    });
    var orgkz = kz;
    var kz = kz.split("-");
    kza = new Array('A','AA','AB','ABG','AC','AE','AIC','AK','AM','AN','ANA','ANG','ANK','AÖ','AP','APD','ARN','ART','AS','ASL','ASZ','AT','AU','AUR','AW','AZ','AZE','B','BA','BAD','BAR','BB','BBG','BBL','BC','BD','BED','BEL','BER','BGL','BI','BIR','BIT','BIW','BL','BLK','BM','BN','BNA','BO','BÖ','BOR','BOT','BP','BRA','BRB','BRG','BS','BSK','BT','BTF','BÜS','BÜZ','BW','BWL','BYL','BZ','C','CA','CB','CE','CHA','CLP','CO','COC','COE','CUX','CW','D','DA','DAH','DAN','DAU','DB','DBR','DD','DE','DEG','DEL','DGF','DH','DL','DLG','DM','DN','DO','DON','DS','DU','DÜW','DW','DZ','E','EA','EB','EBE','ED','EE','EF','EH','EI','EIC','EIL','EIS','EL','EM','EMD','EMS','EN','ER','ERB','ERH','ES','ESA','ESW','EU','EW','F','FB','FD','FDS','FF','FFB','FG','FI','FL','FLÖ','FN','FO','FOR','FR','FRG','FRI','FRW','FS','FT','FTL','FÜ','FW','G','GA','GAP','GC','GDB','GE','GER','GF','GG','GHA','GHC','GI','GL','GM','GMN','GNT','GÖ','GP','GR','GRH','GRM','GRS','GRZ','GS','GT','GTH','GÜ','GUB','GVM','GW','GZ','H','HA','HAL','HAM','HAS','HB','HBN','HBS','HC','HD','HDH','HDL','HE','HEF','HEI','HEL','HER','HET','HF','HG','HGN','HGW','HH','HHM','HI','HIG','HL','HM','HN','HO','HOL','HOM','HOT','HP','HR','HRO','HS','HSK','HST','HU','HV','HVL','HWI','HX','HY','HZ','IGB','IK','IL','IN','IZ','J','JB','JE','JL','K','KA','KB','KC','KE','KEH','KF','KG','KH','KI','KIB','KL','KLE','KLZ','KM','KN','KO','KÖT','KR','KS','KT','KU','KÜN','KUS','KW','KY','KYF','L','LA','LAU','LB','LBS','LBZ','LC','LD','LDK','LDS','LER','LEV','LG','LI','LIB','LIF','LIP','LL','LM','LN','LÖ','LÖB','LOS','LSA','LSN','LSZ','LU','LUK','LWL','M','MA','MAB','MB','MC','MD','ME','MEI','MEK','MER','MG','MGN','MH','MHL','MI','MIL','MK','MKK','ML','MM','MN','MOL','MOS','MQ','MR','MS','MSP','MST','MTK','MÜ','MÜR','MVL','MW','MYK','MZ','MZG','N','NAU','NB','ND','NDH','NE','NEA','NEB','NES','NEW','NF','NH','NI','NK','NL','NM','NMB','NMS','NOH','NOL','NOM','NP','NR','in NWR','NU','NVP','NW','NWM','NY','NZ','OA','OAL','OB','OBG','OC','OD','OE','OF','OG','OH','OHA','OHV','OHZ','OK','OL','OPR','OR','OS','OSL','OVL','OVP','OZ','P','PA','PAF','PAN','PB','PCH','PE','PER','PF','PI','PIR','PK','PL','PLÖ','PM','PN','PR','PS','PW','PZ','QFT','QLB','R','RA','RC','RD','RDG','RE','REG','RG','RH','RIE','RL','RM','RN','RO','ROS','ROW','RP','RPL','RS','RSL','RT','RU','RÜD','RÜG','RV','RW','RZ','S','SAD','SAL','SAW','SB','SBG','SBK','SC','SCZ','SDH','SDL','SDT','SE','SEB','SEE','SFA','SFB','SFT','SG','SGH','SH','SHA','SHG','SHK','SHL','SI','SIG','SIM','SK','SL','SLF','SLN','SLS','SLZ','SM','SN','SO','SOK','SÖM','SON','SP','SPB','SPN','SR','SRB','SRO','ST','STA','STB','STD','STL','SU','SÜW','SW','SZ','SZB','TBB','TET','TF','TG','THL','THW','TIR','TO','TÖL','TP','TR','TS','TÜ','TUT','UE','UEM','UER','UH','UL','UM','UN','V','VB','VEC','VER','VIE','VK','VS','W','WAF','WAK','WB','WBS','WDA','WE','WEN','WES','WF','WHV','WI','WIL','WIS','WK','WL','WLG','WM','WMS','WN','WND','WO','WOB','WR','WRN','WSF','WST','WSW','WT','WTM','WÜ','WUG','WUN','WUR','WW','WZL','X','Y','Z','ZE','ZI','ZP','ZR','ZS','ZW','ZZ');
    lks = new Array('Augsburg in Bayern','in BayernernAalen Ostalbkreis in Baden-Württemberg','Aschaffenburg in Bayernern','Altenburger in Thüringen','Aachen in NWR','Auerbach in Sachsen','Aichach-Friedberg in Bayern','Altenkirchen/Westerwald Rheinland Pfalz','Amberg in Bayern','Ansbach in Bayern','Annaberg in Sachsen','Angermünde im Land Brandenburg-','Anklam in Mecklenburg Vorpommern-','Altötting in Bayern','Apolda - Weimarer in Thüringen','Apolda in Thüringen','Arnstadt in Thüringen','Artern in Thüringen','Amberg-Sulzbach in Bayern','Aschersleben in Sachsen Anhalt','Aue-Schwarzenberg in Sachsen','Altentreptow in Mecklenburg Vorpommern-','Aue in Sachsen','Aurich in Niedersachsen','Bad Neuenahr-Ahrweiler Rheinland Pfalz','Alzey-Worms Rheinland Pfalz','Anhalt-Zerbst in Sachsen Anhalt','Berlin Land Berlin','Bamberg in Bayern','Baden-Baden in Baden-Württemberg','Barnim im Land Brandenburg','Böblingen in Baden-Württemberg','Bernburg in Sachsen Anhalt','im Land Brandenburg Landesregierung und Landtag','Biberach/Riß in Baden-Württemberg','Bundestag, Bundesrat, Bundesregierung','im Land Brandenburgd-Erbisdorf in Sachsen','Belzig im Land Brandenburg','Bernau bei Berlin im Land Brandenburg','Berchtesgadener Land in Bayern','Bielefeld in NWR','Birkenfeld/Nahe und Idar-Oberstein Rheinland Pfalz','Bitburg-Prüm Rheinland Pfalz','Bischofswerda in Sachsen','Zollernalbkreis / Balingen in Baden-Württemberg','Burgenlandkreis in Sachsen Anhalt','Erftkreis / Bergheim in NWR','Bonn in NWR','Borna in Sachsen','Bochum in NWR','Bördekreis-Oschersleben in Sachsen Anhalt','Borken / Ahaus in NWR','Bottrop in NWR','Bundespolizei','Wesermarsch / Brake in Niedersachsen','im Land Brandenburg im Land Brandenburg','Burg in Sachsen Anhalt-','Braunschweig in Niedersachsen','Beeskow im Land Brandenburg','in Bayernreuth in Bayern','Bitterfeld in Sachsen Anhalt','Büsingen am Hochrhein in Baden-Württemberg','Bützow in Mecklenburg Vorpommern-','Bundes-Wasser- und Schiffahrtsverwaltung','Baden-Württemberg Landesregierung und Landtag','in Bayernern Landesregierung und Landtag','Bautzen in Sachsen','Chemnitz in Sachsen','Calau im Land Brandenburg','Cottbus im Land Brandenburg','Celle in Niedersachsen','Cham/Oberpfalz in Bayern','Cloppenburg in Niedersachsen','Coburg in Bayern','Cochem-Zell/Mosel Rheinland Pfalz','Coesfeld/Westfalen in NWR','Cuxhaven in Niedersachsen','Calw in Baden-Württemberg','Düsseldorf in NWR','Darmstadt-Dieburg Hess','Dachau in Bayern','Lüchow-Dannenberg in Niedersachsen','Daun Eifel Rheinland Pfalz','Deutsche Bahn','Bad Doberan in Mecklenburg Vorpommern','Dresden in Sachsen','Dessau in Sachsen Anhalt','Deggendorf in Bayern','Delmenhorst in Niedersachsen','Dingolfing-Landau in Bayern','Diepholz-Syke in Niedersachsen','Döbeln in Sachsen','Dillingen/Donau in Bayern','Demmin in Mecklenburg Vorpommern','Düren in NWR','Dortmund in NWR','Donau-Ries / Donauwörth in Bayern','Dahme-Spreewald im Land Brandenburg','Duisburg in NWR','Bad Dürkheim / Neustadt/Weinstraße Rheinland Pfalz','Dippoldiswalde-Weißeritzkreis in Sachsen','Delitzsch in Sachsen','Essen in NWR','Eisenach in Thüringen','Eilenburg in Sachsen','Ebersberg in Bayern','Erding in Bayern','Elbe-Elster im Land Brandenburg','Erfurt in Thüringen','Eisenhüttenstadt im Land Brandenburg','Eichstätt in Bayern','Eichsfeld in Thüringen','Eisleben in Sachsen Anhalt-','Eisenberg in Thüringen','Emsland / Meppen in Niedersachsen','Emmendingen in Baden-Württemberg','Emden in Niedersachsen','Rhein-Lahn-Kreis / Bad Ems Rheinland Pfalz','Ennepe-Ruhr-Kreis / Schwelm in NWR','Erlangen/Stadt in Bayern','Odenwaldkreis / Erbach Hess','Erlangen-Höchstadt in Bayern','Esslingen/Neckar in Baden-Württemberg','Eisenach in Thüringen','Werra-Meißner-Kreis / Eschwege Hess','Euskirchen in NWR','Eberswalde im Land Brandenburg','Frankfurt/Main Hess','Wetteraukreis / Friedberg Hess','Fulda Hess','Freudenstadt in Baden-Württemberg','Frankfurt/Oder im Land Brandenburg','Fürstenfeldbruck in Bayern','Freiberg/Sachsen in Sachsen','Finsterwalde im Land Brandenburg','Flensburg in Schleswig-Holstein','Flöha in Sachsen','Bodenseekreis / Friedrichshafen in Baden-Württemberg','Forchheim in Bayern','Forst im Land Brandenburg','Freiburg/Breisgau in Baden-Württemberg','Freyung-Grafenau in Bayern','Friesland / Jever in Niedersachsen','Bad Freienwalde im Land Brandenburg','Freising in Bayern','Frankenthal/Pfalz Rheinland Pfalz','Freital in Sachsen','Fürth in Bayern','Fürstenwalde im Land Brandenburg','Gera in Thüringen','Gardelegen in Sachsen Anhalt-','Garmisch-Partenkirchen in Bayern','Glauchau - Chemnitzer Land in Sachsen','Gadebusch in Mecklenburg Vorpommern-','Gelsenkirchen in NWR','Germersheim Rheinland Pfalz','Gifhorn in Niedersachsen','Groß-Gerau Hess','Geithain in Sachsen','Gräfenhainichen in Sachsen Anhalt-','Gießen Hess','Rheinisch-Bergischer Kreis / Bergisch Gladbach in NWR','Oberbergischer Kreis / Gummersbach in NWR','Grimmen in Mecklenburg Vorpommern-','Genthin in Sachsen Anhalt-','Göttingen in Niedersachsen','Göppingen in Baden-Württemberg','Görlitz in Sachsen','Grossenhain in Sachsen','Grimma in Sachsen','Gransee im Land Brandenburg','Greiz in Thüringen','Goslar in Niedersachsen','Gütersloh / Rheda-Wiedenbrück in NWR','Gotha in Thüringen','Güstrow in Mecklenburg Vorpommern','Guben im Land Brandenburg','Grevesmühlen in Mecklenburg Vorpommern-','Greifswald/Landkreis in Mecklenburg Vorpommern-','Günzburg in Bayern','Hannover in Niedersachsen','Hagen/Westfalen in NWR','Halle/Saale in Sachsen Anhalt','Hamm/Westfalen in NWR','Haßberge / Haßfurt in Bayern','Hansestadt Bremen und Bremerhaven Bre','Hildburghausen in Thüringen','Halberstadt in Sachsen Anhalt','Hainichen in Sachsen','Rhein-Neckar-Kreis und Heidelberg in Baden-Württemberg','Heidenheim/Brenz in Baden-Württemberg','Haldensleben in Sachsen Anhalt-','Helmstedt in Niedersachsen','Bad Hersfeld-Rotenburg Hess','Dithmarschen / Heide/Holstein in Schleswig-Holstein','Hessen Landesregierung und Landtag','Herne in NWR','Hettstedt in Sachsen Anhalt-','Herford / Kirchlengern in NWR','Hochtaunuskreis / Bad Homburg v.d.H. Hess','Hagenow in Mecklenburg Vorpommern-','Hansestadt Greifswald in Mecklenburg Vorpommern','Hansestadt Hamburg Hbg','Hohenmölsen in Sachsen Anhalt-','Hildesheim in Niedersachsen','Heiligenstadt in Thüringen','Hansestadt Lübeck in Schleswig-Holstein','Hameln-Pyrmont in Niedersachsen','Heilbronn/Neckar in Baden-Württemberg','Hof/Saale in Bayern','Holzminden in Niedersachsen','Saar-Pfalz-Kreis / Homburg/SaarlandSaarland','Hohenstein-Ernstthal in Sachsen','Bergstraße / Heppenheim Hess','Schwalm-Eder-Kreis / Homberg Hess','Hansestadt Rostock in Mecklenburg Vorpommern','Heinsberg in NWR','Hochsauerlandkreis / Meschede in NWR','Hansestadt Stralsund in Mecklenburg Vorpommern','Hanau Hess','Havelberg in Sachsen Anhalt-','Havelland im Land Brandenburg','Hansestadt Wismar in Mecklenburg Vorpommern','Höxter in NWR','Hoyerswerda in Sachsen','Herzberg im Land Brandenburg','St. Ingbert Saarland','Ilm-Kreis in Thüringen','Ilmenau in Thüringen','Ingolstadt/Donau in Bayern','Itzehoe in Schleswig-Holstein','Jena in Thüringen','Jüterbog im Land Brandenburg','Jessen in Sachsen Anhalt-','Jerichower Land in Sachsen Anhalt','Köln in NWR','Karlsruhe in Baden-Württemberg','Waldeck-Frankenberg / Korbach Hess','Kronach in Bayern','Kempten/Allgäu in Bayern','Kelheim in Bayern','Kaufbeuren in Bayern','Bad Kissingen in Bayern','Bad Kreuznach Rheinland Pfalz','Kiel in Schleswig-Holstein','Donnersberg-Kreis / Kirchheimbolanden Rheinland Pfalz','Kaiserslautern Rheinland Pfalz','Kleve in NWR','Klötze in Sachsen Anhalt-','Kamenz in Sachsen','Konstanz in Baden-Württemberg','Koblenz Rheinland Pfalz','Köthen in Sachsen Anhalt','Krefeld in NWR','Kassel Hess','Kitzingen in Bayern','Kulmbach in Bayern','Hohenlohe-Kreis / Künzelsau in Baden-Württemberg','Kusel Rheinland Pfalz','Königs-Wusterhausen im Land Brandenburg','Kyritz im Land Brandenburg','Kyffhäuserkreis in Thüringen','Leipzig in Sachsen','Landshut in Bayern','Nürnberger Land / Lauf/Pegnitz in Bayern','Ludwigsburg in Baden-Württemberg','Lobenstein in Thüringen','Lübz in Mecklenburg Vorpommern-','Luckau in Sachsen','Landau/Pfalz Rheinland Pfalz','Lahn-Dill-Kreis / Wetzlar Hess','Dahme-Spreewald im Land Brandenburg','Leer/Ostfriesland in Niedersachsen','Leverkusen in NWR','Lüneburg in Niedersachsen','Lindau/Bodensee in Bayern','Bad Liebenwerda im Land Brandenburg','Lichtenfels in Bayern','Lippe / Detmold in NWR','Landsberg/Lech in Bayern','Limburg-Weilburg/Lahn Hess','Lübben im Land Brandenburg','Lörrach in Baden-Württemberg','Löbau in Sachsen','Oder-Spree im Land Brandenburg','Sachsen-Anhalt Landesregierung und Landtag','Sachsen Landesregierung und Landtag','Bad Langensalza in Thüringen','Ludwigshafen/Rhein Rheinland Pfalz','Luckenwalde im Land Brandenburg','Ludwigslust in Mecklenburg Vorpommern','München in Bayern','Mannheim in Baden-Württemberg','Marienberg in Sachsen','Miesbach in Bayern','Malchin in Mecklenburg Vorpommern-','Magdeburg in Sachsen Anhalt','Mettmann in NWR','Meißen in Sachsen','Mittlerer Erzgebirgskreis in Sachsen','Merseburg in Sachsen Anhalt-','Mönchengladbach in NWR','Meiningen in Thüringen','Mülheim/Ruhr in NWR','Mühlhausen in Thüringen','Minden-Lübbecke/Westfalen in NWR','Miltenberg in Bayern','Märkischer Kreis / Lüdenscheid in NWR','Main-Kinzig-Kreis Hess','Mansfelder Land in Sachsen Anhalt','Memmingen in Bayern','Unterallgäu / Mindelheim in Bayern','Märkisch-Oderland im Land Brandenburg.','Neckar-Odenwald-Kreis / Mosbach in Baden-Württemberg','Merseburg-Querfurt in Sachsen Anhalt','Marburg-Biedenkopf/Lahn Hess','Münster/Westfalen in NWR','Main-Spessart-Kreis / Karlstadt in Bayern','Mecklenburg-Strelitz in Mecklenburg Vorpommern','Main-Taunus-Kreis / Hofheim Hess','Mühldorf am Inn in Bayern','Müritz in Mecklenburg Vorpommern','Mecklenburg-Vorpommern Landesregierung und Landtag','Mittweida in Sachsen','Mayen-Koblenz Rheinland Pfalz','Mainz-Bingen und Mainz Rheinland Pfalz','Merzig-Wadern Saarland','Nürnberg in Bayern','Nauen im Land Brandenburg','Neuim Land Brandenburg in Mecklenburg Vorpommern','Neuburg-Schrobenhausen/Donau in Bayern','Nordhausen in Thüringen','Neuss in NWR','Neustadt-Bad Windsheim/Aisch in Bayern','Nebra/Unstrut in Sachsen Anhalt-','Rhön-Grabfeld / Bad Neustadt/Saale in Bayern','Neustadt/Waldnaab in Bayern','Nordfriesland / Husum in Schleswig-Holstein','Neuhaus/Rennsteig in Thüringen','Nienburg/Weser in Niedersachsen','Neunkirchen/SaarlandSaarland','in Niedersachsen Landesregierung und Landtag','Neumarkt/Oberpfalz in Bayern','Naumburg/Saale in Sachsen Anhalt-','Neumünster in Schleswig-Holstein','Grafschaft Bentheim / Nordhorn in Niedersachsen','Niederschlesischer Oberlausitzkreis in Sachsen','Northeim in Niedersachsen','Neuruppin im Land Brandenburg','Neuwied/Rhein Rheinland Pfalz','Nordrhein-Westfalen Landesregierung und Landtag','Neu-Ulm in Bayern','Nordvorpommern in Mecklenburg Vorpommern','Neustadt/Weinstraße Rheinland Pfalz','Nordwestmecklenburg in Mecklenburg Vorpommern','Niesky in Sachsen','Neustrelitz in Mecklenburg Vorpommern-','Oberallgäu / Sonthofen in Bayern','Ostallgäu / Marktoberdorf in Bayern','Oberhausen/Rheinland in NWR','Osterburg in Sachsen Anhalt-','Oschersleben in Sachsen Anhalt-','Stormarn / Bad Oldesloe in Schleswig-Holstein','Olpe in NWR','Offenbach/Main Hess','Ortenaukreis / Offenburg in Baden-Württemberg','Ostholstein / Eutin in Schleswig-Holstein','Osterode/Harz in Niedersachsen','Oranienburg Oberhavel im Land Brandenburg','Osterholz-Scharmbeck in Niedersachsen','Ohre-Kreis in Sachsen Anhalt','Oldenburg in Niedersachsen','Ostprignitz-Ruppin im Land Brandenburg','Oranienburg im Land Brandenburg','Osnabrück in Niedersachsen','Senftenberg - Oberspreewald-Lausitz im Land Brandenburg','Obervogtland / Klingenthal und Ölsnitz in Sachsen','Ostvorpommern in Mecklenburg Vorpommern','Oschatz in Sachsen','Potsdam im Land Brandenburg','Passau in Bayern','Pfaffenhofen/Ilm in Bayern','Rottal-Inn / Pfarrkirchen in Bayern','Paderborn in NWR','Parchim in Mecklenburg Vorpommern','Peine in Niedersachsen','Perleberg im Land Brandenburg','Enzkreis und Pforzheim in Baden-Württemberg','Pinneberg in Schleswig-Holstein','Pirna - Sächsische Schweiz in Sachsen','Pritzwalk im Land Brandenburg','Plauen in Sachsen','Plön/Holstein in Schleswig-Holstein','Belzig - Potsdam-Mittelmark im Land Brandenburg','Pössneck in Thüringen','Prignitz / Perleberg im Land Brandenburg','Pirmasens Rheinland Pfalz','Pasewalk in Mecklenburg Vorpommern-','Prenzlau im Land Brandenburg','Querfurt in Sachsen Anhalt-','Quedlinburg in Sachsen Anhalt','Regensburg in Bayern','Rastatt in Baden-Württemberg','Reichenbach/Vogtland in Sachsen','Rendsburg-Eckernförde in Schleswig-Holstein','Ribnitz-Damgarten in Mecklenburg Vorpommern-','Recklinghausen / Marl in NWR','Regen in Bayernr. Wald in Bayern','Riesa-Großenhain in Sachsen','Roth/Rednitz in Bayern','Riesa in Sachsen','Rochlitz in Sachsen','Röbel/Müritz in Mecklenburg Vorpommern-','Rathenow im Land Brandenburg','Rosenheim in Bayern','Rostock/Landkreis in Mecklenburg Vorpommern-','Rotenburg/Wümme in Niedersachsen','Rhein-Pfalz-Kreis Rheinland Pfalz','Rheinland-Pfalz Landesregierung und Landtag','Remscheid in NWR','Rosslau/Elbe in Sachsen Anhalt-','Reutlingen in Baden-Württemberg','Rudolstadt in Thüringen','Rheingau-Taunus-Kreis / Rüdesheim Hess','Rügen / Bergen in Mecklenburg Vorpommern','Ravensburg in Baden-Württemberg','Rottweil in Baden-Württemberg','Herzogtum Lauenburg / Ratzeburg in Schleswig-Holstein','Stuttgart in Baden-Württemberg','Schwandorf in Bayern','Saarland Landesregierung und Landtag','Altmarkkreis - Salzwedel in Sachsen Anhalt','Saarbrücken Saarland','Strasburg in Mecklenburg Vorpommern-','Schönebeck/Elbe in Sachsen Anhalt','Schwabach in Bayern','Schleiz in Thüringen','Sondershausen in Thüringen','Stendalin Sachsen Anhalt','Schwedt/Oder im Land Brandenburg','Bad Segeberg in Schleswig-Holstein','Sebnitz in Sachsen','Seelow im Land Brandenburg','Soltau-Fallingbostel in Niedersachsen','Senftenberg im Land Brandenburg','Stassfurt in Sachsen Anhalt-','Solingen in NWR','Sangerhausen in Sachsen Anhalt','Schleswig-Holstein Landesregierung und Landtag','Schwäbisch Hall in Baden-Württemberg','Schaumburg / Stadthagen in Niedersachsen','Saale-Holzlandkreis in Thüringen','Suhl in Thüringen','Siegen-Wittgenstein in NWR','Sigmaringen in Baden-Württemberg','Rhein-Hunsrück-Kreis / Simmern Rheinland Pfalz','Saalkreis / Halle in Sachsen Anhalt','Schleswig-Flensburg in Schleswig-Holstein','Saalfeld-Rudolstadt in Thüringen','Schmölln in Thüringen','Saarlouis Saarland','Bad Salzungen in Thüringen','Schmalkalden-Meiningen in Thüringen','Schwerin in Mecklenburg Vorpommern','Soest in NWR','Saale-Orla-Kreis in Thüringen','Sömmerda in Thüringen','Sonneberg in Thüringen','Speyer Rheinland Pfalz','Spremberg im Land Brandenburg','Spree-Neiße im Land Brandenburg','Straubing-Bogen in Bayern','Strausberg im Land Brandenburg','Stadtroda in Thüringen','Steinfurt in NWR','Starnberg in Bayern','Sternberg in Mecklenburg Vorpommern-','Stade in Niedersachsen','Stollberg in Sachsen','Rhein-Sieg-Kreis / Siegburg in NWR','Südl. Weinstraße / Landau Rheinland Pfalz','Schweinfurt in Bayern','Salzgitter in Niedersachsen','Schwarzenberg in Sachsen','Main-Tauber-Kreis / Tauberbischofsheim in Baden-Württemberg','Teterow in Mecklenburg Vorpommern-','Teltow-Fläming im Land Brandenburg','Torgau in Sachsen','Thüringen Landesregierung und Landtag','Technisches Hilfswerk','Tirschenreuth in Bayern','Torgau-Oschatz in Sachsen','Bad Tölz-Wolfratshausen in Bayern','Templin/Uckermark im Land Brandenburg','Trier-Saarburg Rheinland Pfalz','Traunstein in Bayern','Tübingen in Baden-Württemberg','Tuttlingen in Baden-Württemberg','Uelzen in Niedersachsen','Ueckermünde in Mecklenburg Vorpommern-','Uecker-Randow in Mecklenburg Vorpommern','Unstrut-Hainich-Kreis in Thüringen','Alb-Donau-Kreis und Ulm in Baden-Württemberg','Uckermark im Land Brandenburg','Unna/Westfalen in NWR','Vogtlandkreis - Plauen in Sachsen','Vogelsbergkreis / Lauterbach Hess','Vechta in Niedersachsen','Verden/Aller in Niedersachsen','Viersen in NWR','Völklingen Saarland','Schwarzwald-Baar-Kreis / Villingen-Schwenningen in Baden-Württemberg','Wuppertal in NWR','Warendorf in NWR','Wartburgkreis in Thüringen','Wittenberg in Sachsen Anhalt','Worbis in Thüringen','Werdau in Sachsen','Weimar in Thüringen','Weiden/Oberpfalz in Bayern','Wesel / Mörs in NWR','Wolfenbüttel in Niedersachsen','Wilhelmshaven in Niedersachsen','Wiesbaden Hess','Bernkastel-Wittlich/Mosel Rheinland Pfalz','Wismar/Landkreis in Mecklenburg Vorpommern-','Wittstock im Land Brandenburg','Harburg / Winsen/Luhe in Niedersachsen','Wolgast/Usedom in Mecklenburg Vorpommern-','Weilheim-Schongau/Oberin Bayernern in Bayern','Wolmirstedt in Sachsen Anhalt-','Rems-Murr-Kreis / Waiblingen in Baden-Württemberg','St. Wendel Saarland','Worms Rheinland Pfalz','Wolfsburg in Niedersachsen','Wernigerode in Sachsen Anhalt','Waren/Müritz in Mecklenburg Vorpommern-','Weißenfels in Sachsen Anhalt','Ammerland / Westerstede in Niedersachsen','Weißwasser in Sachsen','Waldshut-Tiengen in Baden-Württemberg','Wittmund in Niedersachsen','Würzburg in Bayern','Weißenburg-Gunzenhausen in Bayern','Wunsiedel in Bayern','Wurzen in Sachsen','Westerwald / Montabaur Rheinland Pfalz','Wanzleben in Sachsen Anhalt-','Bundeswehr für NATO-Hauptquartiere','Bundeswehr','Zwickauer Land in Sachsen','Zerbst in Sachsen Anhalt-','Sächsischer Oberlausitzkreis Zittau in Sachsen','Zschopau in Sachsen','Zeulenroda in Thüringen','Zossen im Land Brandenburg','Zweibrücken Rheinland Pfalz','Zeitz in Sachsen Anhalt-');
    var index = kza.indexOf(kz[0]);
    if( kza.indexOf(kz[0]) == -1 ){
        $( "#dialog" ).html("Zum Kennzeichen " + orgkz + " existiert kein Landkreis!");
        $( "#dialog" ).dialog("open");
    };
    if( kza.indexOf(kz[0]) != -1 ){
        $( "#dialog" ).html('Dieses Kennzeichen gehört zum Landkreis <b>' + lks[index] + '</b>');
        $( "#dialog" ).dialog("open");
    }
}


function myclose( param ){
    uri="../firma1.php?Q=C&id=" +  param;
    location.href=uri;
}

function typclose( owner, c_id, task ){
    uri1="lxcmain.php?owner=" + owner;
    uri2="&c_id=" + c_id;
    uri3="&task=" + task;
    uri=uri1+uri2+uri3;
    location.href=uri;
}

function lxc_auf( c_id, owner, task ){
    uri1="lxcauf.php?c_id=" +  c_id;
    uri2="&owner=" + owner;
    uri3="&task=" + task;
    uri=uri1+uri2+uri3;
    location.href=uri;
}
function special( c_id, owner, task ){
    uri1="special/special.phtml?c_id=" +  c_id;
    uri2="&owner=" + owner;
    uri3="&task=" + task;
    uri=uri1+uri2+uri3;
    location.href=uri;
}

function FhzTyp( c_id, owner, hsn, tsn ){
    uri1="FhzTyp.php?c_id=" +  c_id;
    uri2="&owner=" + owner;
    uri3="&hsn=" + hsn;
    uri4="&tsn=" + tsn;
    uri=uri1+uri2+uri3+uri4;
    location.href=uri;
}

function zeigeMotor( owner, c_id, mkbinput ){
    uri1="lxcmotSuche.php?c_id=" +  c_id;
    uri2="&owner=" + owner;
    uri3="&mkbinput=" + mkbinput;
    uri=uri1+uri2+uri3;
    location.href=uri;
}

function lxcaufschliessen( c_id, owner, task, b){
    if(b==1){
        uri="lxcaufSuche.php"
    }
    else{
        uri1="lxcauf.php?c_id=" + c_id;
        uri2="&owner=" + owner;
        uri3="&task=" + task;
        uri=uri1+uri2+uri3;
    }
    location.href=uri;
}

function lxcaufschliessen2( c_id, owner, task){
        uri1="lxcmain.php?owner=" + owner;
        uri2="&c_id=" + c_id;
        uri3="&task=" + task;
        uri=uri1+uri2+uri3;
        location.href=uri;
}

function lxcaufdrucken( a_id ){
    uri="lxcaufPrt.php?a_id=" + a_id;
    //alert(uri);
    location.href=uri;
}

function lxcchown(c_id){
    uri="lxcChown.php?c_id=" + c_id;
}

function mann( zu2, zu3 ){
    location.href="http://catalog.mann-filter.com/EU/ger/vehicle/" + zu2 + "/" + zu3.substring( 0, 3 );//
}

function bmw( zu2, fin ){
    if( zu2 != "0005" ){alert("Das ist kein BMW!"); return;};
    var finsub = fin.substring(10, 17);
    uri1="http://www.realoem.com/bmw/select.do?vin=" + finsub;
    location.href=uri1;
}

function kbaToAutoData(zu2,zu3){
    alert("KBA wird zu AutoData uebergebn zu2 = "+ zu2 +" und zu3 = "+zu3);
}

function kbaToCoParts(zu2,zu3){
    alert("KBA wird zu CoParts uebergebn zu2 = "+ zu2 +" und zu3 = "+zu3);
}

function kbaToEsi(zu2,zu3){
    alert("KBA wird zu EsiTronic uebergebn zu2 = "+ zu2 +" und zu3 = "+zu3);
}

function kbaToEtKa(zu2,zu3){
    alert("FIN wird zu Etka uebergebn zu2 = "+ zu2 +" und zu3 = "+zu3);
}

function kbaToTecDoc(zu2,zu3){
    alert("KBA wird zu TecDoc uebergebn zu2 = "+ zu2 +" und zu3 = "+zu3);
}

function feinstaub(){
    fenster = window.open( "",
        "LxCars - Feinstaubplakette ermitteln", // Name des neuen Fensters
        +"toolbar=0" // Toolbar
        +",location=0" // Adress-Leiste
        +",directories=0" // Zusatzleisten
        +",status=0" // Statusleiste
        +",menubar=0" // Menü
        +",scrollbars=0" // Scrollbars
        +",resizable=0" // Fenstergrösse veränderbar?
        +",width=760" // Fensterbreite in Pixeln
        +",height=730" // Fensterhöhe in Pixeln
    );
    fenster.location.href = "http://dekra.de/feinstaub";
}

function checkFelder(){

    $("#dialog").empty()
    //Testvariable ob submit abgeschickt wird! false = keine Speicherung / true = Speicherung der Daten
    var wert = UniqueKz();
        wert = UniqueFin( document.car.fin.value,document.car.c_id.value );
    if (!document.car.c_ln.value.match(/^[A-Z ÜÄÖ]{1,3}-[A-Z]{1,2}[0-9]{1,4}[H]{0,1}$/)&& document.car.chk_c_ln.checked) {
        $("#dialog").append('Kennzeichen fehlerhaft! Folgendes Format verwenden: MOL-RK73 oder MOL-DS88H für Oldtimer.<br></br>');
        wert = false;
    }
    if ((!(document.car.c_2.value.length <= 0)&&(!document.car.c_2.value.match(/^[0-9]{4}$/)))&& document.car.chk_c_2.checked) {
        $("#dialog").append('Die Schlüsselnummer zu 2.2 ist fehlerhaft! Folgendes Format verwenden: 0600<br></br>');
        wert = false;
    }
    if ((!(document.car.c_3.value.length <= 0)&&(!document.car.c_3.value.match(/^([0-9A-Z]{3,10})$/))) && document.car.chk_c_ln.checked ) {
        $("#dialog").append('Die Schlüsselnummer zu 2.3 ist fehlerhaft! Folgendes Format verwenden: ABL1277L3 oder 300<br></br>');
        wert = false;
    }
    if ((!(document.car.c_em.value.length <= 0)&&(!document.car.c_em.value.match(/^[0-9]{0,2}[0-9A-Z]{4}$/)))&& document.car.chk_c_em.checked) {
    // if ((!(document.car.c_em.value.length <= 0)&&(!document.car.c_em.value.match(/^[0-9]{4,6}$/)))&& document.car.chk_c_em.checked) {
        $("#dialog").append('Der Abgasschlüssel ist fehlerhaft! Folgendes Format verwenden: 0456 oder 010456<br></br>');
        wert = false;
    }
    if ((!(document.car.fin.value.length <= 0)&&(!checkfin(document.car.fin.value,document.car.cn.value))) && document.car.chk_fin.checked) {
        $("#dialog").append('Die Fahrzeugidentnummer (FIN) ist fehlerhaft! Folgendes Format verwenden: WDB2081091X123456. Prüfziffer nicht vergessen. Falls unbekannt \'-\' eingeben<br></br>');
        wert = false;
    }
    if (!(document.car.c_hu.value.length <= 0)&&(!document.car.c_hu.value.match(/^[\d]{1,2}[.][\d]{1,2}[.][\d]{0,4}$/))) {
        $("#dialog").append('Das Datum der HU wurde fehlerhaft eingegeben! Folgendes Format verwenden: 12.8. oder 12.8.13<br></br>');
        wert = false;
    }
    if (!(document.car.c_d.value.length <= 0)&&(!document.car.c_d.value.match(/^[\d]{1,2}[.][\d]{1,2}[.][\d]{1,4}$/))) {
        $("#dialog").append('Das Datum der Erstzulassung wurde fehlerhaft eingegeben! Folgendes Format verwenden: 12.8.73 oder 12.8.<br></br>');
        wert = false;
    }

    //Anzeige des Dialogfeldes
    if (!wert) {
        $("#dialog").dialog({
            width: 500,
            modal: true,
            title: 'LxCars Fehler!',
            open: function(event, ui) {
                // "X"-Button entfernen/verstecken
                $(this).parent().children().children(".ui-dialog-titlebar-close").hide();
            } ,
            buttons: {
                 "OK": function () {
                             $(this).dialog("close");
                     }
            }
        });
      $("#dialog").append('Fehler beheben um die Daten zu speichen!');
      $("#dialog").dialog("open");

    }
    return wert;
  }

function checkhu( param ){
    hudate=param;
    var jetzt = new Date();
    Tag = hudate.substr(0,2);
    Monat = hudate.substr(3,2);
    Jahr = hudate.substr(6,4);
    USDatum = Monat + "/" + Tag + "/" + Jahr;
    if ( Date.parse(USDatum) < jetzt.getTime() && document.car.chk_c_hu.checked ) {
        $("#hu_dialog").dialog({ modal: true});
        $("#hu_dialog").html("Die Hauptuntersuchung ist seit dem " + param + " abgelaufen");//ToDo
        $("#hu_dialog").dialog("open");
        return false;
    }
}

function checkfin( fin, cn ){
     sum = 0;
    if(cn=='-'){return true;}
    if(cn==''){return false;}
    mult = new Array(9,8,7,6,5,4,3,2,10,9,8,7,6,5,4,3,2);
    for(i in mult){
        sum+=(mult[i])*(EBtoNum(fin[i]));
   }
   check=sum%11;
    if(check==10){checkchar='X';}
    else{checkchar=check;}
    if(cn==checkchar){return true;}
    else{return false;}
}

function EBtoNum( fin ){
    if(fin=='O'||fin=='0'){return 0;}
    if(fin=='A'||fin=='J'||fin=='1'){return 1;}
    if(fin=='B'||fin=='K'||fin=='S'||fin=='2'){return 2;}
    if(fin=='C'||fin=='L'||fin=='T'||fin=='3'){return 3;}
    if(fin=='D'||fin=='M'||fin=='U'||fin=='4'){return 4;}
    if(fin=='E'||fin=='N'||fin=='V'||fin=='5'){return 5;}
    if(fin=='F'||fin=='W'||fin=='6'){return 6;}
    if(fin=='G'||fin=='P'||fin=='X'||fin=='7'){return 7;}
    if(fin=='H'||fin=='Q'||fin=='Y'||fin=='8'){return 8;}
    if(fin='I'||fin=='R'||fin=='Z'||fin=='9'){return 9;}
    else{alert("EBtoFin Error!!!");}
}

function UniqueKz(){
    var kz = $("#c_ln").val();
    var c_id = $("#c_id").val();
    if( valid_kz(  kz ) == -1 ){
        $( "#dialog" ).empty();
        $( "#dialog" ).append( 'Das Kennzeichen <b>'+kz+'</b> ist ungültig.');
        $( "#dialog" ).dialog( "open" );
    }
    var returnValue = null;
    $.ajax({ url: "ajax.php", data: { kz: kz, id: c_id }, async: false}).done( function(data){
        if(data != 0){
            $( "#dialog" ).empty();
            $( "#dialog" ).append( 'Ein Datensatz mit dem Kennzeichen <b>'+kz+'</b> existiert bereits. \nDas Fahrzeug gehört <b>'+ data +'</b>.');
            $( "#dialog" ).dialog( "open" );
            returnValue = false;
        }
        else {
            returnValue = true;
        }
    })
    return returnValue;
}

function UniqueFin( fin, c_id ){
    var returnValue = null;
    $.ajax({ url: "ajax.php", data: { fin: fin, id: c_id }, async: false}).done( function(data){
        if(data != 0){
            $( "#dialog" ).empty();
            $( "#dialog" ).append( 'Ein Datensatz mit der FIN <b>'+fin+'</b> existiert bereits. \nDas Fahrzeug '+ data );
            $( "#dialog" ).dialog( "open" );
            returnValue = false;
        }
        else {
            returnValue = true;
        }
    })
    return returnValue;
}

function SucheFin( zu2, zu3 ){
    $.ajax({ url: "ajax.php", data: { fin_zu2: zu2, fin_zu3: zu3 }, async: false}).done( function(data){
        $("#fin").val( data );
    })
}

function SucheMkb( zu2, zu3 ){
    $.ajax({ url: "ajax.php", data: { mkb_zu2: zu2, mkb_zu3: zu3 }, async: false}).done( function(data){
        $("#mkbdrop").append( data );
    })
}
