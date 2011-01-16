using System;
using System.Collections.Generic;
using System.Linq;
using System.Net;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Documents;
using System.Windows.Input;
using System.Windows.Media;
using System.Windows.Media.Animation;
using System.Windows.Shapes;
using Microsoft.Phone.Controls;
using Microsoft.Phone.Notification;
using System.Diagnostics;
using System.Collections.ObjectModel;
using System.IO.IsolatedStorage;

namespace PushNotificationTest
{
    public partial class MainPage : PhoneApplicationPage
    {
        // Constructor
        public MainPage()
        {
            CreatingANotificationChannel();
            InitializeComponent();
        }

        public HttpNotificationChannel myChannel;
        protected readonly string ChannelName = "MPNSTest";
        protected readonly string ServiceURL = "http://<your service url>/";
        protected readonly string NotificationUserIdSettingKey = "notification_user_id";

        public void CreatingANotificationChannel()
        {
            // 既存のチャンネルを探す
            myChannel = HttpNotificationChannel.Find(ChannelName);

            if (myChannel == null)
            {
                // チャンネルがなければ作成する
                myChannel = new HttpNotificationChannel(ChannelName);
                SetUpDelegates();

                // Openすると、ChannelUriUpdated が発行される
                myChannel.Open();

                myChannel.BindToShellToast();
            }
            else
            {
                SetUpDelegates();
            }

            // サービスを登録する
            if (myChannel.ChannelUri != null)
            {
                RegistToService(myChannel.ChannelUri.ToString());
            }
        }

        public void SetUpDelegates()
        {
            // イベントを定義する
            myChannel.ChannelUriUpdated += new EventHandler<NotificationChannelUriEventArgs>(myChannel_ChannelUriUpdated);
            myChannel.HttpNotificationReceived += new EventHandler<HttpNotificationEventArgs>(myChannel_HttpNotificationReceived);
            myChannel.ShellToastNotificationReceived += new EventHandler<NotificationEventArgs>(myChannel_ShellToastNotificationReceived);
            myChannel.ErrorOccurred += new EventHandler<NotificationChannelErrorEventArgs>(myChannel_ErrorOccurred);
        }

        void myChannel_ChannelUriUpdated(object sender, NotificationChannelUriEventArgs e)
        {
            // サービスにチャンネルを登録する
            Debug.WriteLine("Notification channel URI:" + e.ChannelUri.ToString());

            string channel = e.ChannelUri.ToString();
            RegistToService(channel);
        }

        private void RegistToService(string channel)
        {
            // すでにユーザーIDを持ってる場合はそれに対して更新をかける
            string serviceUri;
            if (IsolatedStorageSettings.ApplicationSettings.Contains(NotificationUserIdSettingKey))
            {
                serviceUri = string.Format("{0}?action_api_regist=1&channel={1}&user_id={2}", ServiceURL, Uri.EscapeDataString(channel), 
                    IsolatedStorageSettings.ApplicationSettings[NotificationUserIdSettingKey]);
            }
            else
            {
                serviceUri = string.Format("{0}?action_api_regist=1&channel={1}", ServiceURL, Uri.EscapeDataString(channel));
            }

            // サービスに登録する
            WebClient wc = new WebClient();
            wc.DownloadStringCompleted += delegate(object dl_sender, DownloadStringCompletedEventArgs dl_e)
            {
                Debug.WriteLine(dl_e.Result);
                string[] results = dl_e.Result.Split('\n');

                // 成功していればユーザーIDを設定に保持する
                if (results[0] == "success")
                {
                    if (IsolatedStorageSettings.ApplicationSettings.Contains(NotificationUserIdSettingKey)) {
                        IsolatedStorageSettings.ApplicationSettings[NotificationUserIdSettingKey] = results[1];
                    }
                    else {
                        IsolatedStorageSettings.ApplicationSettings.Add(NotificationUserIdSettingKey, results[1]);
                    }
                }
            };
            Debug.WriteLine("Sending:" + serviceUri);
            wc.DownloadStringAsync(new Uri(serviceUri));
        }

        // トーストからアプリが起動したときの処理
        void myChannel_ShellToastNotificationReceived(object sender, NotificationEventArgs e)
        {
            if (e.Collection != null)
            {
                Dictionary<string, string> collection = (Dictionary<string, string>)e.Collection;
                System.Text.StringBuilder messageBuilder = new System.Text.StringBuilder();

                foreach (string elementName in collection.Keys)
                {
                    //...
                }
            }
        }

        void myChannel_ErrorOccurred(object sender, NotificationChannelErrorEventArgs e)
        {
            switch (e.ErrorType)
            {
                case ChannelErrorType.ChannelOpenFailed:
                    // ...
                    break;
                case ChannelErrorType.MessageBadContent:
                    // ...
                    break;
                case ChannelErrorType.NotificationRateTooHigh:
                    // ...
                    break;
                case ChannelErrorType.PayloadFormatError:
                    // ...
                    break;
                case ChannelErrorType.PowerLevelChanged:
                    // ...
                    break;
            }
        }
    }
}