import java.awt.*;
import java.awt.image.BufferedImage;
import java.io.*;
import javax.imageio.ImageIO;
import javax.comm.*;
import com.topaz.sigplus.*;

public class SigPlusImageSave{
	public static void main( String Args[] ){
		System.out.println(Args[0]);
		SigPlus MyObj = new SigPlus();
		/*MyObj.setSigString(Args[0]);
		MyObj.setImageXSize(500);
		MyObj.setImageYSize(165);
		MyObj.setImageXSize(500);
		MyObj.setImagePenWidth(11);
		MyObj.setImageJustifyMode(5);*/
		//BufferedImage signImage = MyObj.sigImage();
		//save(signImage, Args[1], "jpg");
	}

	private static void save(BufferedImage image, String path, String ext) {
        String fileName = "savingAnImage";
        File file = new File(fileName + "." + ext);
        try {
            ImageIO.write(image, ext, file);  // ignore returned boolean
        } catch(IOException e) {
            System.out.println("Write error for " + file.getPath() +
                               ": " + e.getMessage());
        }
    }
}