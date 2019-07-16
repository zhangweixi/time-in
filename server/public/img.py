from PIL import Image
import numpy as np
import os
import pdfkit
import sys


def handimg1(threshold, img, res):
    #threshold = 95
    temp = Image.open(img)
    (w,h) = temp.size
    w = int(w/2)
    h = int(h/2)
    Img = temp.convert('L')
    
    table = []
    for i in range(256):
        if i < threshold:
            table.append(0)
        else:
            table.append(1)
    photo = Img.point(table, '1')
    photo.save(res)
    temp2 = Image.open(res)
    out = temp2.resize((w,h),Image.ANTIALIAS)
    out.save(res)



# 正在使用的方法
def handimg2(threshold, img, res):

    img = np.array(Image.open(img).convert('L'))
    rows, cols = img.shape

    for i in range(rows):
        for j in range(cols):
            if (img[i, j] < threshold):
                pass
                #img[i, j] =
            else:
                img[i, j] = 255

    newimg = Image.fromarray(img)
    newimg.save(res)


def create_pdf(imgpath):
    body = """  <html>  <head>  <meta name="pdfkit-page-size" content="Legal"/>  <meta name="pdfkit-orientation" content="Landscape"/>  </head>  <body>"""
    for root, dirs, files in os.walk(imgpath):
        for file in files:
            img = imgpath + "/" + file
            body = body + "<img src='" + img + "'/>"
    body = body + """</body>  </html>  """

    pdfkit.from_string(body, imgpath+"/out.pdf")


def test():
    imgpath = "./img"
    for root, dirs, files in os.walk(imgpath):
        for file in files:
            #handimg1(110, imgpath + "/" + file, imgpath+"/b"+file)
            handimg2(150, imgpath + "/" + file, "res/"+file)
            #handimg1(90, "res/" + file, "res/b"+file)
            #handimg2(180, imgpath+"/b"+file,imgpath+"/b"+file)
#test()


if __name__ == "__main__":
    if len(sys.argv) != 3:
        exit('just need two params')

    img = sys.argv[1]

    if os.path.exists(img) == False:
        
        exit("file \""+img+"\" not fund")

    if sys.argv[2] == "quick": 
        handimg1(90,img,img) # 快速预览
    else:
        handimg2(150,img,img)




