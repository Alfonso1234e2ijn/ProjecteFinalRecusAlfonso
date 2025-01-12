# Avaluació d'Accessibilitat i Usabilitat en el Desenvolupament Web

## 1. Preparació

### Tasques a Provar
Les tres tasques bàsiques que un usuari hauria de poder realitzar en la teva aplicació web són:
1. **Crear un grup/thread nou**:  
   Els usuaris haurien de ser capaços de crear un grup o thread de manera fàcil i ràpida, escollint temes o categories, i afegint participants si s'escau.
2. **Enviar un missatge en un grup/thread existent**:  
   Els usuaris han de poder escriure i enviar missatges de manera clara, amb una interfície d'usuari intuïtiva.
3. **Valorar un missatge amb like o dislike**:  
   Aquesta funcionalitat permet que els usuaris interactuïn amb els missatges dels altres, i és important que el procés sigui senzill i immediat.

### Verificació d'Accessibilitat
Utilitzant eines com **Wave** o l'auditoria d'accessibilitat de **Chrome DevTools**, es va verificar els següents punts clau:

- **Contrast de colors**:  
  Els colors utilitzats en la pàgina estan ben equilibrats, amb un contrast adequat entre els textos i els fons. Això facilita la lectura per a persones amb dificultats de visió. El sistema de colors ha estat dissenyat amb una atenció especial al contrast, obtenint una valoració positiva.

- **Ús de gradients i hover**:  
  L'ús de gradients en el disseny aporta un toc visual atractiu sense comprometre la llegibilitat. A més, els efectes de hover (canvis visuals en el pas del cursor sobre elements interactius) són clars i evidents, millorant la interactivitat i usabilitat de la pàgina.

- **Tamanys de textos**:  
  Els textos tenen un tamany adequat per a una lectura còmoda, i la tipografia s'adapta bé a dispositius de diferents mides de pantalla. Això ajuda a garantir que el contingut sigui accessible tant en dispositius mòbils com en pantalles més grans.

#### Valoració General
A partir de la valoració de l'accessibilitat i les proves realitzades, es va obtenir una puntuació de **9.5** en la valoració general de la pàgina.  
Aquesta puntuació reflecteix un disseny que compleix amb molts dels estàndards d'accessibilitat, però encara pot millorar-se en certs aspectes com l'optimització del contrast en algunes seccions.

## 2. Realització de Proves amb Usuaris

### Selecció d'Usuaris
1. **Usuari A**: Mare i pare (45 i 47 anys respectivament) sense experiència amb aplicacions web o només un ús bàsic.
2. **Usuari B**: Germà (17 anys) que fa servir aplicacions web amb freqüència, però no és expert.
3. **Usuari C**: Amic programador de l’Escorial (conegut a les pràctiques), amb àmplia experiència en aplicacions web.

### Execució de les Proves
- **Usuari A**:  
  Va necessitar més temps i orientació per completar les tasques. La interfície podria millorar per a usuaris amb poc coneixement en aplicacions web.
- **Usuari B**:  
  Va trobar la pàgina bastant intuïtiva, però encara es poden fer algunes millores en la visibilitat de la interacció de vots.
- **Usuari C**:  
  Va realitzar les tasques ràpidament i va identificar punts de millora subtils, com la retroalimentació visual després de crear un grup o votar.

## 3. Anàlisi i Proposta de Millores

### Resum dels Resultats
#### Observacions
- **Usuari A**: Va tenir dificultats per trobar la secció de creació de grups/threads, especialment si la interfície és molt densa o poc intuitiva.
- **Usuari B**: Va trobar l'optimització del sistema de votació (like/dislike) clara, però es va frustrar perquè no es mostrava immediatament el nombre de vots (like/dislike).
- **Usuari C**: Va detectar que la funcionalitat de valoració d'usuaris no era del tot evident i va suggerir millorar la visibilitat del sistema de valoració dels usuaris.

#### Problemes Identificats
- **Alta Prioritat**:
  - Falta de claretat en la creació de grups/threads.
  - Dificultat per comprendre el sistema de valoració d'usuaris.
- **Mitjana Prioritat**:
  - Millorar la visibilitat del nombre de vots en temps real.
  - Problemes de contrast de colors en certes seccions de la pàgina.
- **Baixa Prioritat**:
  - El teclat no és completament navegable en totes les seccions.

### Propostes de Millora
#### Accessibilitat
1. **Millorar el contrast de colors**:  
   Fes servir colors amb un contrast més fort per als textos, especialment als botons de like/dislike i els formularis de creació de grups.
2. **Afegir etiquetes ARIA**:  
   Assegura't que tots els botons i formularis estiguin ben etiquetats per lectors de pantalla, incloent descripcions clares per a l'ús dels botons de "like" i "dislike".
3. **Navegabilitat via teclat**:  
   Millora la navegabilitat mitjançant teclat, de manera que tots els elements interactius siguin accessibles sense ratolí.

#### Usabilitat
1. **Millorar la interfície de creació de grups/threads**:  
   Utilitza un assistent o guia pas a pas per ajudar als usuaris a crear grups i afegir participants de manera intuïtiva.
2. **Mostrar el nombre de vots en temps real**:  
   Inclou un sistema de notificacions que mostri el nombre de vots de manera immediata un cop s'hagi realitzat una acció de valoració.
3. **Visualitzar la valoració d'usuaris de manera més destacada**:  
   Afegeix icones o destacats visuals a les valoracions per fer-les més visibles i comprensibles.

## 4. Implementació
Si tens temps, implementa algunes de les millores proposades, com ara la millora de la navegabilitat amb teclat i l'optimització de la visibilitat del sistema de vots. Després, repeteix les proves d'usabilitat amb els mateixos usuaris per validar les millores implementades.
