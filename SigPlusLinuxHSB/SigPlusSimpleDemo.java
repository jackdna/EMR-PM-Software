import java.awt.*;
import java.awt.event.*;
import java.beans.*;
/*import com.gemtools.sigplus.*;*/

/*import javax.comm.*;*/
import java.io.*;

public class SigPlusSimpleDemo extends Frame
   {
  /* SigPlus              sigObj = null;*/

   public static void main( String Args[] )
	  {
	  SigPlusSimpleDemo demo = new SigPlusSimpleDemo();
	  }

	public SigPlusSimpleDemo()
	{



		try
		{
			/*ClassLoader cl = (com.gemtools.sigplus.SigPlus.class).getClassLoader();
			sigObj = (SigPlus)Beans.instantiate(cl, "com.gemtools.sigplus.SigPlus");

			setLayout(new GridLayout(1, 1));
			add(sigObj);
			pack();
			setTitle("DemoSigPlus");

			addWindowListener(new WindowAdapter()
			{
				public void windowClosing(WindowEvent we)
				{
					sigObj.setTabletState(0);
					System.exit(0);
				}

				public void windowClosed(WindowEvent we)
				{
					System.exit(0);
				}
			});

			sigObj.addSigPlusListener(new SigPlusListener()
			{
				public void handleTabletTimerEvent(SigPlusEvent0 evt)
				{
				}

				public void handleNewTabletData(SigPlusEvent0 evt)
				{
				}

				public void handleKeyPadData(SigPlusEvent0 evt)
				{
				}
			});


			setSize(640, 256);
			show();

			sigObj.setTabletModel("SignatureGemLCD1X5");
			sigObj.setTabletComPort("HID1");
			//sigObj.setTabletComPort("/dev/ttyS0"); //change as necessary
			sigObj.setTabletState(1);*/
		}
		catch (Exception e)
		{
			return;
		}
	}
   }