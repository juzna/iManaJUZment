CREATE TABLE APGroupTags (id BIGINT UNIQUE, tag_id BIGINT, PRIMARY KEY(id, tag_id)) ENGINE = INNODB;
CREATE TABLE APGroupTagList (id BIGINT AUTO_INCREMENT, name VARCHAR(255) UNIQUE, PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE AP (id BIGINT UNIQUE AUTO_INCREMENT, nazev VARCHAR(100) NOT NULL UNIQUE, popis VARCHAR(255), mode VARCHAR(255) DEFAULT 'other' NOT NULL, ip VARCHAR(15) NOT NULL, netmask SMALLINT NOT NULL, pvid INT DEFAULT 1 NOT NULL, snmpallowed TINYINT(1) DEFAULT '0' NOT NULL, snmpcommunity VARCHAR(50), snmppassword VARCHAR(50), snmpversion VARCHAR(10), realm VARCHAR(50), username VARCHAR(50), pass VARCHAR(50), os VARCHAR(20) NOT NULL, osversion VARCHAR(20), sshfingerprint VARCHAR(60), l3parent BIGINT, l3parentif VARCHAR(50), posx BIGINT, posy BIGINT, ulice VARCHAR(100), cislopopisne VARCHAR(100), mesto VARCHAR(100), psc VARCHAR(10), stat VARCHAR(10), uir_obec BIGINT, uir_cobce BIGINT, uir_ulice BIGINT, uir_objekt BIGINT, uir_special bool, PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE APAntena (id BIGINT AUTO_INCREMENT, ap BIGINT, interface VARCHAR(50) NOT NULL, smer MEDIUMINT NOT NULL, rozsah MEDIUMINT NOT NULL, dosah INT NOT NULL, polarita VARCHAR(255) NOT NULL, pasmo SMALLINT, poznamka VARCHAR(255), INDEX ap_idx (ap), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE APHw (id BIGINT AUTO_INCREMENT, ap BIGINT NOT NULL, skladpolozka BIGINT, serial VARCHAR(50), INDEX ap_idx (ap), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE APHwIf (id BIGINT AUTO_INCREMENT, aphw BIGINT NOT NULL, interface VARCHAR(50) NOT NULL, mac VARCHAR(20), typ VARCHAR(255) NOT NULL, skladpolozka BIGINT, INDEX aphw_idx (aphw), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE APIP (id BIGINT AUTO_INCREMENT, ap BIGINT NOT NULL, interface VARCHAR(50) NOT NULL, ip VARCHAR(15) NOT NULL, netmask SMALLINT NOT NULL, poznamka VARCHAR(255), INDEX ap_idx (ap), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE APParams (ap BIGINT, name VARCHAR(50), value VARCHAR(50), comment VARCHAR(255), PRIMARY KEY(ap, name)) ENGINE = INNODB;
CREATE TABLE APParent (id BIGINT AUTO_INCREMENT, parentap BIGINT NOT NULL, parentinterface VARCHAR(50), parentport VARCHAR(50), parentvlan INT NOT NULL, childap BIGINT NOT NULL, childinterface VARCHAR(50), childport VARCHAR(50), childvlan INT NOT NULL, comment VARCHAR(255), INDEX childap_idx (childap), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE APPokryti (id BIGINT AUTO_INCREMENT, ap BIGINT NOT NULL, interface VARCHAR(50), vlan INT, adresa BIGINT NOT NULL, poznamka VARCHAR(255), doporuceni TINYINT DEFAULT 3 NOT NULL, INDEX ap_idx (ap), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE APPokrytiSubnet (id BIGINT AUTO_INCREMENT, pokryti BIGINT NOT NULL, ip VARCHAR(15) NOT NULL, netmask SMALLINT NOT NULL, INDEX pokryti_idx (pokryti), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE APPort (id BIGINT AUTO_INCREMENT, ap BIGINT NOT NULL, port VARCHAR(50) NOT NULL, typ VARCHAR(255) NOT NULL, porcis BIGINT, odbernaadresa BIGINT, cislovchodu VARCHAR(20), cislobytu VARCHAR(20), connectedto BIGINT, connectedinterface VARCHAR(50), connectedport VARCHAR(50), isuplink TINYINT(1) DEFAULT '0' NOT NULL, popis VARCHAR(255), INDEX ap_idx (ap), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE APPortVlan (ap BIGINT, port VARCHAR(50), vlan INT, tagged TINYINT(1) DEFAULT '0' NOT NULL, pvid TINYINT(1) DEFAULT '0' NOT NULL, INDEX a_p_id_idx (a_p_id), PRIMARY KEY(ap, port, vlan)) ENGINE = INNODB;
CREATE TABLE APRoute (id BIGINT AUTO_INCREMENT, ap BIGINT NOT NULL, ip VARCHAR(15) NOT NULL, netmask SMALLINT NOT NULL, gateway VARCHAR(15) NOT NULL, preferedsource VARCHAR(15), distance SMALLINT DEFAULT 1 NOT NULL, popis VARCHAR(255), enabled TINYINT(1) DEFAULT '1' NOT NULL, INDEX ap_idx (ap), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE APService (id BIGINT AUTO_INCREMENT, ap BIGINT NOT NULL, service VARCHAR(50) NOT NULL, state VARCHAR(20) NOT NULL, statetext VARCHAR(100), lastcheck DATETIME, UNIQUE INDEX IX_APService_APservice_idx (ap, service), INDEX ap_idx (ap), INDEX service_idx (service), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE APServiceList (code VARCHAR(20), nazev VARCHAR(50) NOT NULL, popis VARCHAR(255), PRIMARY KEY(code)) ENGINE = INNODB;
CREATE TABLE APServiceOSList (id BIGINT AUTO_INCREMENT, code VARCHAR(20) NOT NULL, os VARCHAR(20) NOT NULL, version VARCHAR(20) DEFAULT '%' NOT NULL, INDEX code_idx (code), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE APSwIf (id BIGINT AUTO_INCREMENT, ap BIGINT, interface VARCHAR(50) NOT NULL, masterinterface VARCHAR(50), type VARCHAR(255), isnet TINYINT(1) DEFAULT '0' NOT NULL, bssid VARCHAR(20), essid VARCHAR(30), frequency INT, enabled TINYINT(1) DEFAULT '1' NOT NULL, enctype VARCHAR(255), enckey VARCHAR(50), tarifflag BIGINT DEFAULT 1 NOT NULL, UNIQUE INDEX IX_APswif_APif_idx (ap, interface), INDEX ap_idx (ap), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE APVlan (ap BIGINT, vlan INT, poznamka VARCHAR(255), PRIMARY KEY(ap, vlan)) ENGINE = INNODB;
CREATE TABLE Adresar (id BIGINT AUTO_INCREMENT, nazev VARCHAR(100), jeplatcedph TINYINT(1) DEFAULT '0' NOT NULL, zobrazit TINYINT(1) DEFAULT '1' NOT NULL, PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE AdresarAdresa (id BIGINT, adresar BIGINT NOT NULL, isodberna TINYINT(1) DEFAULT '0' NOT NULL, isfakturacni TINYINT(1) DEFAULT '0' NOT NULL, iskorespondencni TINYINT(1) DEFAULT '0' NOT NULL, popis VARCHAR(255), firma VARCHAR(100), firma2 VARCHAR(100), titulpred VARCHAR(50), jmeno VARCHAR(50), druhejmeno VARCHAR(50), prijmeni VARCHAR(50), druheprijmeni VARCHAR(50), titulza VARCHAR(50), ulice VARCHAR(50), cislopopisne VARCHAR(20), mesto VARCHAR(50), psc VARCHAR(10), uir_objekt BIGINT, ico VARCHAR(20), dic VARCHAR(20), poznamka VARCHAR(255), rodnecislo VARCHAR(20), datumnarozeni VARCHAR(20), INDEX adresar_idx (adresar), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE AdresarKontakt (id BIGINT UNIQUE AUTO_INCREMENT, adresar BIGINT NOT NULL, typ VARCHAR(10) NOT NULL, hodnota VARCHAR(100) NOT NULL, popis VARCHAR(255), INDEX adresar_idx (adresar), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE AdresarUcet (id BIGINT AUTO_INCREMENT, adresar BIGINT, predcisli VARCHAR(10) NOT NULL, cislo VARCHAR(10) NOT NULL, kodbanky VARCHAR(4) NOT NULL, poznamka VARCHAR(255), INDEX adresar_idx (adresar), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE AdresarZalohovyUcet (id BIGINT AUTO_INCREMENT, adresar BIGINT, nazev VARCHAR(100) NOT NULL, kod TINYINT DEFAULT 1 NOT NULL, UNIQUE INDEX IX_AdresarZalohovyUcet_adresar_kod_idx (adresar, kod), INDEX adresar_idx (adresar), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE Platba (id BIGINT AUTO_INCREMENT, PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE Tarif (id BIGINT AUTO_INCREMENT, nazev VARCHAR(50) NOT NULL UNIQUE, zakladni TINYINT(1) DEFAULT '1' NOT NULL, mesicnipausal FLOAT(18, 2) NOT NULL, ctvrtletnipausal FLOAT(18, 2) NOT NULL, pololetnipausal FLOAT(18, 2) NOT NULL, rocnipausal FLOAT(18, 2) NOT NULL, popis VARCHAR(255) NOT NULL, posilatfaktury TINYINT(1) DEFAULT '0' NOT NULL, PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE TarifFlag (id BIGINT UNIQUE AUTO_INCREMENT, nazev VARCHAR(50) NOT NULL UNIQUE, PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE TarifRychlost (tarif BIGINT, flag BIGINT, txmin VARCHAR(20), rxmin VARCHAR(20), txmax VARCHAR(20), rxmax VARCHAR(20), txburst VARCHAR(20), rxburst VARCHAR(20), txtresh VARCHAR(20), rxtresh VARCHAR(20), txtime VARCHAR(20), rxtime VARCHAR(20), txpriority SMALLINT, rxpriority SMALLINT, PRIMARY KEY(tarif, flag)) ENGINE = INNODB;
CREATE TABLE Uhrada (id BIGINT AUTO_INCREMENT, platba BIGINT, INDEX platba_idx (platba), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE ZakanzikTarifUhrada (id BIGINT AUTO_INCREMENT, tarif BIGINT NOT NULL, mesicu MEDIUMINT NOT NULL, datumod DATE NOT NULL, datumdo DATE NOT NULL, INDEX tarif_idx (tarif), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE Zakaznik (porcis BIGINT, cislosmlouvy BIGINT UNIQUE, heslo VARCHAR(50), aktivniod DATE, accepted TINYINT(1) DEFAULT '0' NOT NULL, accepteduser BIGINT, acceptedtime DATETIME, predplaceno DATE, aktivni TINYINT(1) DEFAULT '1', starydluh BIGINT DEFAULT 0, nepocitatpredplatne TINYINT(1) DEFAULT '0' NOT NULL, nepocitatpredplatneduvod VARCHAR(255), instalacnipoplatek FLOAT(18, 2), doporucitel BIGINT, sepsanismlouvy DATE, neplaticskupina BIGINT, neplatictolerance BIGINT, neplaticneresitdo DATE, vychoziadresa BIGINT, latitude DOUBLE(18, 2), longitude DOUBLE(18, 2), deleted_at DATETIME, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX cisloSmlouvy_idx (cislosmlouvy), INDEX vychoziadresa_idx (vychoziadresa), PRIMARY KEY(porcis)) ENGINE = INNODB;
CREATE TABLE ZakaznikAdresa (id BIGINT AUTO_INCREMENT, porcis BIGINT NOT NULL, isodberna TINYINT(1) DEFAULT '0' NOT NULL, isfakturacni TINYINT(1) DEFAULT '0' NOT NULL, iskorespondencni TINYINT(1) DEFAULT '0' NOT NULL, popis VARCHAR(255), firma VARCHAR(100), firma2 VARCHAR(100), titulpred VARCHAR(50), jmeno VARCHAR(50), druhejmeno VARCHAR(50), prijmeni VARCHAR(50), druheprijmeni VARCHAR(50), titulza VARCHAR(50), ico VARCHAR(20), dic VARCHAR(20), poznamka VARCHAR(255), rodnecislo VARCHAR(20), datumnarozeni VARCHAR(20), posx BIGINT, posy BIGINT, ulice VARCHAR(100), cislopopisne VARCHAR(100), mesto VARCHAR(100), psc VARCHAR(10), stat VARCHAR(10), uir_obec BIGINT, uir_cobce BIGINT, uir_ulice BIGINT, uir_objekt BIGINT, uir_special bool, INDEX porcis_idx (porcis), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE ZakaznikIP (id BIGINT AUTO_INCREMENT, porcis BIGINT NOT NULL, ip VARCHAR(20) NOT NULL, netmask SMALLINT NOT NULL, ipold VARCHAR(20), ipverej VARCHAR(20), mac VARCHAR(20), visiblemac VARCHAR(20), adresa BIGINT NOT NULL, l2parent BIGINT, l2parentif VARCHAR(50), l3parent BIGINT, l3parentif VARCHAR(50), poznamka VARCHAR(255), enctype VARCHAR(255) DEFAULT 'none' NOT NULL, enckey VARCHAR(50), router VARCHAR(255) DEFAULT 'none' NOT NULL, routervlastni TINYINT(1) DEFAULT '0', voip VARCHAR(255) DEFAULT 'none' NOT NULL, vlastnirychlost TINYINT(1) DEFAULT '0' NOT NULL, apip VARCHAR(15), apmac VARCHAR(20), txmin VARCHAR(20), rxmin VARCHAR(20), txmax VARCHAR(20), rxmax VARCHAR(20), txburst VARCHAR(20), rxburst VARCHAR(20), txtresh VARCHAR(20), rxtresh VARCHAR(20), txtime VARCHAR(20), rxtime VARCHAR(20), txpriority SMALLINT, rxpriority SMALLINT, posx BIGINT, posy BIGINT, INDEX porcis_idx (porcis), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE ZakaznikKontakt (id BIGINT UNIQUE AUTO_INCREMENT, porcis BIGINT NOT NULL, typ VARCHAR(10) NOT NULL, hodnota VARCHAR(100) NOT NULL, popis VARCHAR(255), INDEX porcis_idx (porcis), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE ZakaznikNeaktivni (id BIGINT, porcis BIGINT NOT NULL, datumod DATE NOT NULL, datumdo DATE, duvod VARCHAR(255) NOT NULL, INDEX porcis_idx (porcis), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE ZakaznikNekativniTarif (id BIGINT AUTO_INCREMENT, tarif BIGINT NOT NULL, neaktivita BIGINT NOT NULL, INDEX tarif_idx (tarif), INDEX neaktivita_idx (neaktivita), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE ZakaznikTarif (id BIGINT AUTO_INCREMENT, porcis BIGINT NOT NULL, tarif BIGINT NOT NULL, popis VARCHAR(255), zakladni TINYINT(1) DEFAULT '0' NOT NULL, specialniceny TINYINT(1) DEFAULT '0' NOT NULL, mesicnipausal FLOAT(18, 2), ctvrtletnipausal FLOAT(18, 2), pololetnipausal FLOAT(18, 2), rocnipausal FLOAT(18, 2), datumod DATE NOT NULL, datumdo DATE, predplaceno DATE, aktivni TINYINT(1) DEFAULT '1' NOT NULL, zaplacenocele TINYINT(1), INDEX porcis_idx (porcis), INDEX tarif_idx (tarif), PRIMARY KEY(id)) ENGINE = INNODB;
ALTER TABLE APGroupTags ADD CONSTRAINT APGroupTags_tag_id_APGroupTagList_id FOREIGN KEY (tag_id) REFERENCES APGroupTagList(id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE APGroupTags ADD CONSTRAINT APGroupTags_id_AP_id FOREIGN KEY (id) REFERENCES AP(id);
ALTER TABLE APAntena ADD CONSTRAINT APAntena_ap_AP_id FOREIGN KEY (ap) REFERENCES AP(id);
ALTER TABLE APHw ADD CONSTRAINT APHw_ap_AP_id FOREIGN KEY (ap) REFERENCES AP(id);
ALTER TABLE APHwIf ADD CONSTRAINT APHwIf_aphw_APHw_id FOREIGN KEY (aphw) REFERENCES APHw(id);
ALTER TABLE APIP ADD CONSTRAINT APIP_ap_AP_id FOREIGN KEY (ap) REFERENCES AP(id);
ALTER TABLE APParams ADD CONSTRAINT APParams_ap_AP_id FOREIGN KEY (ap) REFERENCES AP(id);
ALTER TABLE APParent ADD CONSTRAINT APParent_childap_AP_id FOREIGN KEY (childap) REFERENCES AP(id);
ALTER TABLE APPokryti ADD CONSTRAINT APPokryti_ap_AP_id FOREIGN KEY (ap) REFERENCES AP(id);
ALTER TABLE APPokrytiSubnet ADD CONSTRAINT APPokrytiSubnet_pokryti_APPokryti_id FOREIGN KEY (pokryti) REFERENCES APPokryti(id);
ALTER TABLE APPort ADD CONSTRAINT APPort_ap_AP_id FOREIGN KEY (ap) REFERENCES AP(id);
ALTER TABLE APPortVlan ADD CONSTRAINT APPortVlan_a_p_id_AP_id FOREIGN KEY (a_p_id) REFERENCES AP(id);
ALTER TABLE APRoute ADD CONSTRAINT APRoute_ap_AP_id FOREIGN KEY (ap) REFERENCES AP(id);
ALTER TABLE APService ADD CONSTRAINT APService_service_APServiceList_code FOREIGN KEY (service) REFERENCES APServiceList(code);
ALTER TABLE APService ADD CONSTRAINT APService_ap_AP_id FOREIGN KEY (ap) REFERENCES AP(id);
ALTER TABLE APServiceOSList ADD CONSTRAINT APServiceOSList_code_APServiceList_code FOREIGN KEY (code) REFERENCES APServiceList(code);
ALTER TABLE APSwIf ADD CONSTRAINT APSwIf_ap_AP_id FOREIGN KEY (ap) REFERENCES AP(id);
ALTER TABLE APVlan ADD CONSTRAINT APVlan_ap_AP_id FOREIGN KEY (ap) REFERENCES AP(id);
ALTER TABLE AdresarAdresa ADD CONSTRAINT AdresarAdresa_adresar_Adresar_id FOREIGN KEY (adresar) REFERENCES Adresar(id);
ALTER TABLE AdresarKontakt ADD CONSTRAINT AdresarKontakt_adresar_Adresar_id FOREIGN KEY (adresar) REFERENCES Adresar(id);
ALTER TABLE AdresarUcet ADD CONSTRAINT AdresarUcet_adresar_Adresar_id FOREIGN KEY (adresar) REFERENCES Adresar(id);
ALTER TABLE AdresarZalohovyUcet ADD CONSTRAINT AdresarZalohovyUcet_adresar_Adresar_id FOREIGN KEY (adresar) REFERENCES Adresar(id);
ALTER TABLE TarifRychlost ADD CONSTRAINT TarifRychlost_tarif_Tarif_id FOREIGN KEY (tarif) REFERENCES Tarif(id);
ALTER TABLE TarifRychlost ADD CONSTRAINT TarifRychlost_flag_TarifFlag_id FOREIGN KEY (flag) REFERENCES TarifFlag(id);
ALTER TABLE Uhrada ADD CONSTRAINT Uhrada_platba_Platba_id FOREIGN KEY (platba) REFERENCES Platba(id);
ALTER TABLE ZakanzikTarifUhrada ADD CONSTRAINT ZakanzikTarifUhrada_tarif_ZakaznikTarif_id FOREIGN KEY (tarif) REFERENCES ZakaznikTarif(id);
ALTER TABLE Zakaznik ADD CONSTRAINT Zakaznik_vychoziadresa_ZakaznikAdresa_id FOREIGN KEY (vychoziadresa) REFERENCES ZakaznikAdresa(id);
ALTER TABLE ZakaznikAdresa ADD CONSTRAINT ZakaznikAdresa_porcis_Zakaznik_porcis FOREIGN KEY (porcis) REFERENCES Zakaznik(porcis);
ALTER TABLE ZakaznikIP ADD CONSTRAINT ZakaznikIP_porcis_Zakaznik_porcis FOREIGN KEY (porcis) REFERENCES Zakaznik(porcis);
ALTER TABLE ZakaznikKontakt ADD CONSTRAINT ZakaznikKontakt_porcis_Zakaznik_porcis FOREIGN KEY (porcis) REFERENCES Zakaznik(porcis);
ALTER TABLE ZakaznikNeaktivni ADD CONSTRAINT ZakaznikNeaktivni_porcis_Zakaznik_porcis FOREIGN KEY (porcis) REFERENCES Zakaznik(porcis);
ALTER TABLE ZakaznikNekativniTarif ADD CONSTRAINT ZakaznikNekativniTarif_tarif_ZakaznikTarif_id FOREIGN KEY (tarif) REFERENCES ZakaznikTarif(id);
ALTER TABLE ZakaznikNekativniTarif ADD CONSTRAINT ZakaznikNekativniTarif_neaktivita_ZakaznikNeaktivni_id FOREIGN KEY (neaktivita) REFERENCES ZakaznikNeaktivni(id);
ALTER TABLE ZakaznikTarif ADD CONSTRAINT ZakaznikTarif_tarif_Tarif_id FOREIGN KEY (tarif) REFERENCES Tarif(id);
ALTER TABLE ZakaznikTarif ADD CONSTRAINT ZakaznikTarif_porcis_Zakaznik_porcis FOREIGN KEY (porcis) REFERENCES Zakaznik(porcis);