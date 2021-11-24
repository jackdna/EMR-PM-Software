/*
This file is part of EZ Soft's Office Manager.

Office Manager is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

Office Manager is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Office Manager; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

 **** EZ-Soft addendum ****

This program in part or in whole is open source. Any derivative works must be 100%
free and open source. EZ Soft Inc. reserves the right to produce closed source additions and
derivatives of this software. This software's license is same license as the mysql "dual licensing" model.

If you wish to produce a closed source version of software that uses any/all of the code
contained, distribute this software, or use the software in any way that you think
may conflict with the dual license, contact sales@ez-softinc.com.

If you would like customization, additions, support or service also contact sales@ez-softinc.com.

If you would like to see descriptions of Office Manager's code license just look at the "dual licensing" model
for mysql upon which it is based.
http://www.mysql.com/company/legal/licensing/commercial-license.html
http://www.mysql.com/company/legal/licensing/opensource-license.html

Use of this software does not insure HIPAA Compliance.  It is meant to aid in meeting HIPPA Compliance requirements.

 * You can also check out
 http://www.gnu.org/copyleft/gpl.html
 For a more complete description of GPL.

 God bless open source. A FRIEND of capitalism!
*/
package com.ezsoft.app.medical;

import java.awt.*;
import javax.swing.*;
import java.awt.image.BufferedImage;
import java.awt.event.*;
import java.beans.*;

import javax.comm.*;
import java.io.*;

import com.topaz.sigplus.*;

/**
 * <p>Title: EZ Soft's Office Manager</p>
 * <p>Description: Office Management Suite</p>
 * <p>Copyright: Copyright (c) 2003</p>
 * <p>Company: ezsoft.com</p>
 * @author Michael Lee
 * @version 0.9
 */
public class SigPlusTest extends JPanel implements Runnable
{
    public static final int OK = 0;
    public static final int CLEAR = 1;
    public static final int YES = 2;
    public static final int NO = 3;
    public static final int BACK = 4;
    JSigPlus sigObj = null;
    Image[] rawImages;
    boolean newPatient;
    JPanel thePanel;

    static final String fileBase = "images/sigcap/";
    int page = 1;
    boolean start;
    String signature;

    public SigPlusTest()
    {
        init();
        final JFrame f = new JFrame();
        thePanel = new JPanel();
        thePanel.setPreferredSize(new Dimension(295,105));
        f.getContentPane().setLayout(new BorderLayout());
        f.getContentPane().add(thePanel, BorderLayout.CENTER);
        f.setSize(295, 105);
        f.pack();
        f.setTitle("Topaz LCD 1X5 Demo");

/*        MedicalData.inst().addSigCapListenerListener(new SigCapListener()
        {
            public void sigCapUpdated(SigCapEvent evt)
            {
                ASignInPanel sip = new ASignInPanel(evt.isNewPatient(), evt.getSignature());
                f.getContentPane().add(sip, BorderLayout.CENTER);
            }
        });*/

        f.addWindowListener(new WindowAdapter()
        {
            public void windowClosing(WindowEvent we)
            {
                shutdown();
                System.exit(0);
            }
            public void windowClosed(WindowEvent we)
            {
                shutdown();
                System.exit(0);
            }
        });
        f.show();
    }

    public void init()
    {
        int i;
        String fileName;

        try
        {

            String drivername = "com.sun.comm.Win32Driver";
            try
            {
                CommDriver driver = (CommDriver)Class.forName(drivername).newInstance();
                driver.initialize();
            }
            catch(Throwable th)
            {
                /* Discard it */
            }

            ClassLoader cl = (com.gemtools.sigplus.JSigPlus.class).getClassLoader();
            sigObj = (JSigPlus)Beans.instantiate(cl, "com.gemtools.sigplus.JSigPlus");

            sigObj.addSigPlusListener(new SigPlusListener()
            {
                public void handleTabletTimerEvent(SigPlusEvent0 evt){}
                public void handleNewTabletData(SigPlusEvent0 evt){}
                public void handleKeyPadData(SigPlusEvent0 evt)
                {  }
            });

//            sigObj.setTabletModel(MedicalData.SIG_CAP_DEVICE);
            sigObj.setTabletModel("SignatureGemLCD1X5");

            String[] imageTitles =
            {
                "new_patient",
                "SigLine",
                "Ok",
                "Clear",
                "Back"
            };
            MediaTracker mt = new MediaTracker(this);
            rawImages = new Image[imageTitles.length];

            for(i = 0;i < imageTitles.length;i++)
            {
                fileName = fileBase + imageTitles[i] + ".jpg";
                rawImages[i] = Toolkit.getDefaultToolkit().getImage(fileName);
                mt.addImage(rawImages[i], i + 1);
            }
            try
            {
                mt.waitForAll();
            }
            catch(Exception e)
            {
                System.out.println("Error opening bitmap files");
            }

            // set up new patient screen

            setUpNewPatient();
            sigObj.setLCDCaptureMode(2);
//            sigObj.setTabletComPort(MedicalData.SIG_CAP_PORT);
            sigObj.setTabletComPort("HID1");
            sigObj.setTabletState(1);
        }
        catch(Exception e)
        {
            return;
        }
    } // end init()

    public void setUpSignIn()
    {
        sigObj.clearTablet();
        sigObj.keyPadClearSigWindow(1);
        sigObj.keyPadClearHotSpotList();
        sigObj.lcdRefresh(0, 0, 0, 240, 128);
        sigObj.lcdSetWindow(0, 0, 190, 56); // clear screen
        sigObj.keyPadSetSigWindow(1, 0, 0, 190, 56); // set signature capture size
        sigObj.keyPadAddHotSpot(OK, 1, 200, 12, 22, 12); // ok image hot spot
        sigObj.keyPadAddHotSpot(CLEAR, 1, 200, 28, 38, 12); // clear image hot spot
        sigObj.keyPadAddHotSpot(BACK, 1, 200, 44, 33, 12); // clear image hot spot
        sigObj.lcdWriteImage(0, 2, 0, 0, 190, 56, rawImages[1]); // sign in image box
        sigObj.lcdWriteImage(0, 2, 200, 10, 20, 10, rawImages[2]); // ok image
        sigObj.lcdWriteImage(0, 2, 200, 25, 36, 10, rawImages[3]); // clear image
        sigObj.lcdWriteImage(0, 2, 200, 40, 31, 10, rawImages[4]); // back image
    }




    public void setUpNewPatient()
    {
        sigObj.clearTablet();
        sigObj.keyPadClearSigWindow(1);
        sigObj.keyPadClearHotSpotList();
        sigObj.lcdSetWindow(0, 0, 0, 0);
        sigObj.keyPadSetSigWindow(1, 0, 0, 0, 0);
//        sigObj.lcdRefresh(2, 0, 0, 240, 64);
        sigObj.lcdRefresh(0, 0, 0, 240, 128);
        sigObj.lcdWriteImage(0, 2, 0, 0, 240, 55, rawImages[0]); // new Patient screen image
        sigObj.keyPadAddHotSpot(YES, 1, 25, 25, 36, 16); // yes hot spot
        sigObj.keyPadAddHotSpot(NO, 1, 115, 25, 32, 25); // no hot spot
    }

    public void setUpHaveSeat()
    {
        sigObj.lcdRefresh(0, 0, 0, 240, 128);
    }

    public void shutdown()
    {
        sigObj.clearTablet();
        sigObj.keyPadClearSigWindow(1);
        sigObj.keyPadClearHotSpotList();
        sigObj.lcdSetWindow(0, 0, 0, 0);
        sigObj.keyPadSetSigWindow(1, 0, 0, 0, 0);
//        sigObj.lcdRefresh(2, 0, 0, 240, 64);
        sigObj.lcdRefresh(0, 0, 0, 240, 128);
        sigObj.setTabletState(0);
    }

    public static void main(String args[])
    {
        SigPlusTest demo = new SigPlusTest();
        Thread eventThread = new Thread(demo);
        eventThread.start();
      }

      public void run()
      {
          try
          {
              while(true)
              {
                  Thread.sleep(200);
                  if(sigObj.keyPadQueryHotSpot(OK) != 0 && page == 2) // ok pressed pressed
                  {
                      String sigStr;
                      sigStr = sigObj.getSigString();
                      sigObj.keyPadClearSigWindow(1);
                      sigObj.clearTablet();
                      sigObj.setSigString(sigStr);
                      System.out.println("OK");
//                      SigCapEvent sce = new SigCapEvent(this);
//                      sce.setNewPatient(newPatient);

                      sigObj.lcdRefresh(1, 195, 5, 25, 15);
                      sigObj.setTabletState(0);
                      //sigObj.setImageXSize(295);
                      //sigObj.setImageYSize(87);

                      sigObj.setImageXSize(sigObj.getXExtent());
                      sigObj.setImageYSize(sigObj.getYExtent());
//                      sigObj.setImageXSize(295);
//                      sigObj.setImageYSize(105);

                      sigObj.setImagePenWidth(4);
//                      sigObj.setImageJustifyX(10);
//                      sigObj.setImageJustifyY(10);
                      sigObj.setImageJustifyMode(5); // allows limits on sig border
                      BufferedImage bi = sigObj.sigImage();
                      System.out.println("H:"+bi.getHeight()+" W:"+bi.getWidth());
//                      sce.setSignature(bi);
//                      MedicalData.inst().fireSigCapListeners(sce);
                      thePanel.setVisible(false);
                      thePanel.removeAll();
                      thePanel.add(new JLabel(new ImageIcon(bi)));
                      thePanel.setVisible(true);
                      page = 1;
                      setUpNewPatient();
                      sigObj.setTabletState(1);
                  }
                  else if(sigObj.keyPadQueryHotSpot(CLEAR) != 0 && page == 2) // clear pressed
                  {
                      System.out.println("CLEAR");
                      sigObj.clearTablet();
                      sigObj.keyPadClearSigWindow(1);
                      sigObj.setTabletState(0);
                      sigObj.setTabletState(1);
                      sigObj.lcdRefresh(1, 200, 25, 36, 10);
                      sigObj.lcdRefresh(0, 5, 5, 190, 56);
                      sigObj.lcdWriteImage(0, 2, 5, 5, 190, 56, rawImages[1]);
                      sigObj.lcdRefresh(1, 200, 25, 36, 10);
                      sigObj.keyPadClearSigWindow(1);
                  }
                  else if(sigObj.keyPadQueryHotSpot(YES) != 0 && page == 1) // yes pressed
                  {
                      System.out.println("YES");
                      sigObj.clearTablet();
              sigObj.keyPadClearSigWindow(1);
                      sigObj.lcdRefresh(1, 28, 28, 36, 16);
                      page = 2;
                      setUpSignIn();
                      newPatient = true;
                  }
                  else if(sigObj.keyPadQueryHotSpot(NO) != 0 && page == 1)  // no pressed
                  {
                      System.out.println("NO");
                      sigObj.clearTablet();
                      sigObj.keyPadClearSigWindow(1);
                      sigObj.lcdRefresh(1, 118, 25, 32, 25);
                      page = 2;
                      setUpSignIn();
                      newPatient = false;
                  }
                  else if(sigObj.keyPadQueryHotSpot(BACK) != 0 && page == 2)  // no pressed
                  {
                      sigObj.lcdRefresh(1, 200, 40, 31, 10);
                      sigObj.keyPadClearSigWindow(1);
                      sigObj.clearTablet();
                      page = 1;
                      setUpNewPatient();
                  }
                  sigObj.keyPadClearSigWindow(1);
              }
          }
          catch(InterruptedException e)
          {
              System.out.println("sigcap ex:"+e.getMessage());
          }
      } // end run
}
