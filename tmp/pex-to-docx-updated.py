from html.parser import HTMLParser
from xml.sax.saxutils import escape
from zipfile import ZipFile, ZIP_DEFLATED
from pathlib import Path
import base64, sys
class Parser(HTMLParser):
 def __init__(self):
  super().__init__(); self.pages=[]; self.items=[]; self.current=None; self.runs=[]; self.bold=False; self.italic=False; self.center=False; self.skip=False; self.images=[]
 def handle_starttag(self,t,a):
  d=dict(a)
  if t=='section': self.items=[]
  if t=='div' and d.get('class')=='footer': self.skip=True
  if t=='div' and d.get('class')=='center': self.center=True
  if t in ['p','h1','h2','h3','li']: self.current=t; self.runs=[]
  if t in ['b','strong']: self.bold=True
  if t in ['i','em']: self.italic=True
  if t=='br' and self.current: self.runs.append('<w:r><w:br/></w:r>')
  if t=='img':
   self.images.append((base64.b64decode(d['src'].split(',',1)[1]), 'png' if 'image/png' in d['src'] else 'jpeg', int(d.get('data-cx',5486400)), int(d.get('data-cy',7315200))));self.items.append(('image',len(self.images)-1))
 def handle_endtag(self,t):
  if t in ['b','strong']: self.bold=False
  if t in ['i','em']: self.italic=False
  if t in ['p','h1','h2','h3','li'] and self.current:
   props='<w:spacing w:after="160" w:line="355" w:lineRule="auto"/><w:jc w:val="'+('center' if self.center else 'both')+'"/>'
   if t in ['h1','h2','h3']:
    props='<w:pStyle w:val="'+({'h1':'Title','h2':'Heading1','h3':'Heading2'}[t])+'"/><w:keepNext/><w:spacing w:before="'+('400' if t=='h1' else '240')+'" w:after="160"/><w:jc w:val="'+('center' if self.center else 'left')+'"/>'
   if t=='li': props+='<w:ind w:left="280" w:hanging="180"/>';self.runs.insert(0,'<w:r><w:t>• </w:t></w:r>')
   self.items.append(('p','<w:p><w:pPr>'+props+'</w:pPr>'+''.join(self.runs)+'</w:p>'));self.current=None
  if t=='div': self.center=False; self.skip=False
  if t=='section': self.pages.append(self.items)
 def handle_data(self,s):
  if self.current and not self.skip:
   props=('<w:b/>' if self.bold else '')+('<w:i/>' if self.italic else '')
   self.runs.append('<w:r><w:rPr>'+props+'</w:rPr><w:t xml:space="preserve">'+escape(s)+'</w:t></w:r>')
p=Parser();p.feed(Path(sys.argv[1]).read_text(encoding='utf8'))
ns='xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture"'
image='<w:p><w:pPr><w:jc w:val="center"/></w:pPr><w:r><w:drawing><wp:inline><wp:extent cx="5486400" cy="7315200"/><wp:docPr id="1" name="Termo de autorização" descr="Termo preenchido para o Site Capão News"/><a:graphic><a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/picture"><pic:pic><pic:nvPicPr><pic:cNvPr id="0" name="termo.jpeg"/><pic:cNvPicPr/></pic:nvPicPr><pic:blipFill><a:blip r:embed="rIdImage"/><a:stretch><a:fillRect/></a:stretch></pic:blipFill><pic:spPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="5486400" cy="7315200"/></a:xfrm><a:prstGeom prst="rect"><a:avLst/></a:prstGeom></pic:spPr></pic:pic></a:graphicData></a:graphic></wp:inline></w:drawing></w:r></w:p>'
body=[]
for i,page in enumerate(p.pages):
 if i: body.append('<w:p><w:r><w:br w:type="page"/></w:r></w:p>')
 for typ,val in page:
  if typ=='image':
   raw,ext,cx,cy=p.images[val];body.append(image.replace('rIdImage','rIdImage'+str(val)).replace('5486400',str(cx)).replace('7315200',str(cy)).replace('termo.jpeg','image'+str(val)+'.'+ext).replace('Termo de autorização','Registro visual do projeto').replace('Termo preenchido para o Site Capão News','Imagem do projeto Capão News').replace('id="1"','id="'+str(val+1)+'"'))
  else: body.append(val)
sect='<w:sectPr><w:footerReference w:type="default" r:id="rIdFooter"/><w:pgSz w:w="11906" w:h="16838"/><w:pgMar w:top="1247" w:right="1361" w:bottom="1134" w:left="1361" w:header="567" w:footer="567"/></w:sectPr>'
doc='<?xml version="1.0" encoding="UTF-8" standalone="yes"?><w:document '+ns+'><w:body>'+''.join(body)+sect+'</w:body></w:document>'
styles='<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:docDefaults><w:rPrDefault><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial"/><w:sz w:val="22"/><w:color w:val="000000"/></w:rPr></w:rPrDefault></w:docDefaults><w:style w:type="paragraph" w:default="1" w:styleId="Normal"><w:name w:val="Normal"/></w:style>'
for id,name,size in [('Title','Title',40),('Heading1','heading 1',30),('Heading2','heading 2',24)]: styles+='<w:style w:type="paragraph" w:styleId="'+id+'"><w:name w:val="'+name+'"/><w:basedOn w:val="Normal"/><w:rPr><w:b/><w:sz w:val="'+str(size)+'"/><w:color w:val="000000"/></w:rPr></w:style>'
styles+='</w:styles>'
rels='<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
ct='<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Default Extension="jpeg" ContentType="image/jpeg"/><Default Extension="png" ContentType="image/png"/><Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/><Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/><Override PartName="/word/footer1.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.footer+xml"/></Types>'
with ZipFile(sys.argv[2],'w',ZIP_DEFLATED) as z:
 z.writestr('[Content_Types].xml',ct);z.writestr('_rels/.rels',rels+'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/></Relationships>');z.writestr('word/document.xml',doc);z.writestr('word/styles.xml',styles)
 for i,(raw,ext,cx,cy) in enumerate(p.images): z.writestr('word/media/image'+str(i)+'.'+ext,raw)
 z.writestr('word/_rels/document.xml.rels',rels+'<Relationship Id="rIdStyles" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'+''.join('<Relationship Id="rIdImage'+str(i)+'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="media/image'+str(i)+'.'+ext+'"/>' for i,(raw,ext,cx,cy) in enumerate(p.images))+'<Relationship Id="rIdFooter" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/footer" Target="footer1.xml"/></Relationships>')
 z.writestr('word/footer1.xml','<w:ftr xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:p><w:pPr><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:sz w:val="18"/></w:rPr><w:fldChar w:fldCharType="begin"/></w:r><w:r><w:instrText> PAGE </w:instrText></w:r><w:r><w:fldChar w:fldCharType="end"/></w:r></w:p></w:ftr>')
from xml.etree import ElementTree
with ZipFile(sys.argv[2]) as z:
 for name in z.namelist():
  if name.endswith(('.xml','.rels')): ElementTree.fromstring(z.read(name))
 print('DOCX OK',len(p.pages),'seções',len(z.read('word/document.xml')),'bytes de conteúdo')
