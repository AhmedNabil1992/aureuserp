USE [master]
GO
/****** Object:  Database [license]    Script Date: 4/9/2026 10:56:21 PM ******/
CREATE DATABASE [license]
 CONTAINMENT = NONE
 ON  PRIMARY 
( NAME = N'license', FILENAME = N'C:\Program Files\Microsoft SQL Server\MSSQL14.MSSQLSERVER\MSSQL\DATA\license.ndf' , SIZE = 598016KB , MAXSIZE = UNLIMITED, FILEGROWTH = 65536KB )
 LOG ON 
( NAME = N'license_log', FILENAME = N'C:\Program Files\Microsoft SQL Server\MSSQL14.MSSQLSERVER\MSSQL\DATA\license_log.ldf' , SIZE = 2564096KB , MAXSIZE = 2048GB , FILEGROWTH = 65536KB )
GO
ALTER DATABASE [license] SET COMPATIBILITY_LEVEL = 140
GO
IF (1 = FULLTEXTSERVICEPROPERTY('IsFullTextInstalled'))
begin
EXEC [license].[dbo].[sp_fulltext_database] @action = 'enable'
end
GO
ALTER DATABASE [license] SET ANSI_NULL_DEFAULT OFF 
GO
ALTER DATABASE [license] SET ANSI_NULLS OFF 
GO
ALTER DATABASE [license] SET ANSI_PADDING OFF 
GO
ALTER DATABASE [license] SET ANSI_WARNINGS OFF 
GO
ALTER DATABASE [license] SET ARITHABORT OFF 
GO
ALTER DATABASE [license] SET AUTO_CLOSE ON 
GO
ALTER DATABASE [license] SET AUTO_SHRINK OFF 
GO
ALTER DATABASE [license] SET AUTO_UPDATE_STATISTICS ON 
GO
ALTER DATABASE [license] SET CURSOR_CLOSE_ON_COMMIT OFF 
GO
ALTER DATABASE [license] SET CURSOR_DEFAULT  GLOBAL 
GO
ALTER DATABASE [license] SET CONCAT_NULL_YIELDS_NULL OFF 
GO
ALTER DATABASE [license] SET NUMERIC_ROUNDABORT OFF 
GO
ALTER DATABASE [license] SET QUOTED_IDENTIFIER OFF 
GO
ALTER DATABASE [license] SET RECURSIVE_TRIGGERS OFF 
GO
ALTER DATABASE [license] SET  DISABLE_BROKER 
GO
ALTER DATABASE [license] SET AUTO_UPDATE_STATISTICS_ASYNC OFF 
GO
ALTER DATABASE [license] SET DATE_CORRELATION_OPTIMIZATION OFF 
GO
ALTER DATABASE [license] SET TRUSTWORTHY OFF 
GO
ALTER DATABASE [license] SET ALLOW_SNAPSHOT_ISOLATION OFF 
GO
ALTER DATABASE [license] SET PARAMETERIZATION SIMPLE 
GO
ALTER DATABASE [license] SET READ_COMMITTED_SNAPSHOT OFF 
GO
ALTER DATABASE [license] SET HONOR_BROKER_PRIORITY OFF 
GO
ALTER DATABASE [license] SET RECOVERY FULL 
GO
ALTER DATABASE [license] SET  MULTI_USER 
GO
ALTER DATABASE [license] SET PAGE_VERIFY CHECKSUM  
GO
ALTER DATABASE [license] SET DB_CHAINING OFF 
GO
ALTER DATABASE [license] SET FILESTREAM( NON_TRANSACTED_ACCESS = OFF ) 
GO
ALTER DATABASE [license] SET TARGET_RECOVERY_TIME = 60 SECONDS 
GO
ALTER DATABASE [license] SET DELAYED_DURABILITY = DISABLED 
GO
ALTER DATABASE [license] SET QUERY_STORE = OFF
GO
USE [license]
GO
/****** Object:  UserDefinedFunction [dbo].[fn_ConvertMoneyToWordsAr]    Script Date: 4/9/2026 10:56:22 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE FUNCTION [dbo].[fn_ConvertMoneyToWordsAr] (
  @TheNo  numeric(18,3)
)
RETURNS nvarchar(1000)
AS
BEGIN

  declare @decimal_digits int 
  declare @TheNoAfterReplicate nvarchar(15) 
  declare @currency_desc nvarchar(50)
  declare @decimal_digit_desc nvarchar (50)
  declare @ComWithWord nvarchar(1000),@TheNoWithDecimal as nvarchar(400),@ThreeWords as int 
  declare @minus_value int  

  set @minus_value = 0 
  set @ThreeWords = 0 
  set @ComWithWord  = '' 


  -----------------------------------------
  SET @currency_desc = 'جنيه مصري';
  SET @decimal_digit_desc = 'EGP';
  SET @decimal_digits = 2;
  -------------------------------------------


  declare   @Tafket TABLE (num int,  NoName nvarchar(100)) 
  if @TheNo = 0   return 'zero'

  if @TheNo < 0   
  begin 
    set @ComWithWord = @ComWithWord + ' سالب '
    set @TheNo=@TheNo*-1
  end 

  set @TheNoAfterReplicate = right(replicate('0',15)+cast(floor(@TheNo) as nvarchar(15)),15) 



  INSERT INTO @Tafket VALUES (0,'')  
  INSERT INTO @Tafket VALUES (1,' واحد ') 
  INSERT INTO @Tafket VALUES (2, ' اثنان ') 
  INSERT INTO @Tafket VALUES (3,' ثلاثة ') 
  INSERT INTO @Tafket VALUES (4,' اربعة ') 
  INSERT INTO @Tafket VALUES (5,' خمسة ') 
  INSERT INTO @Tafket VALUES (6,' ستة ') 
  INSERT INTO @Tafket VALUES (7,' سبعة ') 
  INSERT INTO @Tafket VALUES (8,' ثمانية ') 
  INSERT INTO @Tafket VALUES (9,' تسعة ') 
  INSERT INTO @Tafket VALUES (10,' عشرة ') 
  INSERT INTO @Tafket VALUES (11,' احدى عشر ') 
  INSERT INTO @Tafket VALUES (12,' اثنى عشر ') 
  INSERT INTO @Tafket VALUES (13,' ثلاثة عشر ') 
  INSERT INTO @Tafket VALUES (14,' اربعة عشر ') 
  INSERT INTO @Tafket VALUES (15,' خمسة عشر ') 
  INSERT INTO @Tafket VALUES (16,' ستة عشر ') 
  INSERT INTO @Tafket VALUES (17,' سبعة عشر ') 
  INSERT INTO @Tafket VALUES (18,' ثمانية عشر ') 
  INSERT INTO @Tafket VALUES (19,' تسعة عشر ') 
  INSERT INTO @Tafket VALUES (20,' عشرون ') 
  INSERT INTO @Tafket VALUES (30,' ثلاثون ') 
  INSERT INTO @Tafket VALUES (40,' اربعون ') 
  INSERT INTO @Tafket VALUES (50,' خمسون ') 
  INSERT INTO @Tafket VALUES (60,' ستون ') 
  INSERT INTO @Tafket VALUES (70,' سبعون ') 
  INSERT INTO @Tafket VALUES (80,' ثمانون ') 
  INSERT INTO @Tafket VALUES (90,' تسعون ') 
  INSERT INTO @Tafket VALUES (100,' مائة ') 
  INSERT INTO @Tafket VALUES (200,' مائتان ') 
  INSERT INTO @Tafket VALUES (300,' ثلاثمائة ') 
  INSERT INTO @Tafket VALUES (400,' اربعمائة ') 
  INSERT INTO @Tafket VALUES (500,' خمسمائة ') 
  INSERT INTO @Tafket VALUES (600,' ستمائة ') 
  INSERT INTO @Tafket VALUES (700,' سبعمائة ') 
  INSERT INTO @Tafket VALUES (800,' ثمانمائة ') 
  INSERT INTO @Tafket VALUES (900,' تسعمائة ') 
  INSERT INTO @Tafket  




  SELECT FirstN.num+LasteN.num,LasteN.NoName+' و '+FirstN.NoName FROM 
  (SELECT * FROM @Tafket WHERE num >= 20 AND num <= 90) FirstN 
  CROSS JOIN 
  (SELECT * FROM @Tafket WHERE num >= 1 AND num <= 9) LasteN

  INSERT INTO @Tafket  
  SELECT FirstN.num+LasteN.num,FirstN.NoName+' و '+LasteN.NoName FROM (SELECT * FROM @Tafket WHERE num >= 100 AND num <= 900) FirstN 
  CROSS JOIN 
  (SELECT * FROM @Tafket WHERE num >= 1 AND num <= 99) LasteN


  if left(@TheNoAfterReplicate,3) > 0 
  set @ComWithWord = @ComWithWord + ISNULL((select NoName  from  @Tafket where num=left(@TheNoAfterReplicate,3)),'')+  ' ترليون' 
  if left(right(@TheNoAfterReplicate,12),3) > 0 and  left(@TheNoAfterReplicate,3) > 0 
  set @ComWithWord=@ComWithWord+ ' و ' 
  if left(right(@TheNoAfterReplicate,12),3) > 0 
  set @ComWithWord = @ComWithWord +ISNULL((select NoName from @Tafket where num=left(right(@TheNoAfterReplicate,12),3)),'') +  ' بليون' 
  if left(right(@TheNoAfterReplicate,9),3) > 0

  begin 
  set @ComWithWord=@ComWithWord + case  when @TheNo>999000000  then ' و '  else '' end 
  set @ThreeWords=left(right(@TheNoAfterReplicate,9),3)
  set @ComWithWord = @ComWithWord + ISNULL((select case when   @ThreeWords>2 then NoName end  from @Tafket  where num=left(right(@TheNoAfterReplicate,9),3)),'')  + case when  @ThreeWords=2 then ' مليونان' when   @ThreeWords between 3 and 10 then ' ملايين' else ' مليون' end 
  end

  if left(right(@TheNoAfterReplicate,6),3) > 0 
  begin 
  set @ComWithWord=@ComWithWord + case  when @TheNo>999000  then ' و '  else '' end 
  set @ThreeWords=left(right(@TheNoAfterReplicate,6),3)
  set @ComWithWord = @ComWithWord + ISNULL((select case when  @ThreeWords>2 then NoName  end from @Tafket where num=left(right(@TheNoAfterReplicate,6),3)),'')+ case when  @ThreeWords=2 then ' الفان' when @ThreeWords between 3 and 10 then ' الاف'  else ' الف' end 
  end

  if right(@TheNoAfterReplicate,3) > 0 
  begin

  if @TheNo>999 
  begin 
  set @ComWithWord=@ComWithWord + ' و ' 
  end

  if right(@TheNoAfterReplicate, 2) = '01' or right(@TheNoAfterReplicate, 2) = '02' 
  begin 
  --set @ComWithWord=@ComWithWord + case  when @TheNo>1000  then ' و'  else '' end 
  --set @ThreeWords=left(right(@TheNoAfterReplicate,6),3)
  set @ComWithWord = @ComWithWord + ' ' + ISNULL((select NoName from @Tafket where num=right(@TheNoAfterReplicate, 3)),'')
  end

  set @ThreeWords=right(@TheNoAfterReplicate,2)

  if @ThreeWords=0 
  begin 
  --   set @ComWithWord=@ComWithWord + ' و' 
     set @ComWithWord = @ComWithWord + ISNULL((select NoName  from @Tafket where @ThreeWords=0 AND num=right(@TheNoAfterReplicate,3)),'') 
  end

  end

  set @ThreeWords=right(@TheNoAfterReplicate,2) 
  set @ComWithWord =  @ComWithWord  +   ISNULL((select  NoName  from @Tafket where @ThreeWords>2 AND num=right(@TheNoAfterReplicate,3)),'') 
  if(LTRIM(RTRIM(@ComWithWord))='') set @ComWithWord = 'صفر'
  set @ComWithWord = @ComWithWord +' '+  @currency_desc 
  if right(rtrim(@ComWithWord),1)=',' set @ComWithWord = substring(@ComWithWord,1,len(@ComWithWord)-1)
  if  right(@TheNo,len(@TheNo)-charindex('.',@TheNo)) >0 and charindex('.',@TheNo)<>0 
      begin 
          set @ThreeWords=left(right(round(@TheNo,3),3),@decimal_digits) 
          SELECT @TheNoWithDecimal=  ' و ' + ISNULL((SELECT NoName from @Tafket where num=left(right(round(@TheNo,3),3),3)  AND @ThreeWords >3),'') 
          set @TheNoWithDecimal = @TheNoWithDecimal+ ' ' + @decimal_digit_desc
  set @ComWithWord = @ComWithWord + ' و '+ CONVERT(nvarchar(4000),@ThreeWords)+ ' ' + @decimal_digit_desc
  END 
  set @ComWithWord = @ComWithWord + ' فقط لا غير '



  return rtrim(@ComWithWord) 

END
GO
/****** Object:  Table [dbo].[activity_log]    Script Date: 4/9/2026 10:56:22 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[activity_log](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[log_name] [nvarchar](255) NULL,
	[description] [nvarchar](max) NOT NULL,
	[subject_type] [nvarchar](255) NULL,
	[subject_id] [bigint] NULL,
	[causer_type] [nvarchar](255) NULL,
	[causer_id] [bigint] NULL,
	[properties] [nvarchar](max) NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
	[event] [nvarchar](255) NULL,
	[batch_uuid] [uniqueidentifier] NULL,
 CONSTRAINT [PK__activity__3213E83FDFB9CD3E] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[ApplicationVersions]    Script Date: 4/9/2026 10:56:22 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[ApplicationVersions](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[ApplicationName] [varchar](50) NULL,
	[VersionNumber] [varchar](50) NULL,
	[UpdateLink] [varchar](255) NULL,
	[ReleaseDate] [date] NULL,
	[FileName] [varchar](255) NULL,
	[AppTerminate] [varchar](max) NULL,
	[IsDBUpdate] [varchar](5) NULL,
	[DBLink] [varchar](255) NULL,
	[Download_Times] [int] NULL,
	[IsActive] [bit] NULL,
	[Remark] [varchar](255) NULL,
 CONSTRAINT [PK__Applicat__3214EC277F31FD7E] PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[articles]    Script Date: 4/9/2026 10:56:22 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[articles](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[product_id] [int] NOT NULL,
	[title] [nvarchar](255) NOT NULL,
	[content] [nvarchar](max) NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
	[author] [varchar](255) NULL,
	[section] [varchar](255) NULL,
 CONSTRAINT [PK__articles__3213E83F3A5ABB5E] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[BOM]    Script Date: 4/9/2026 10:56:22 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[BOM](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[Product_Code] [int] NULL,
	[Item_ID] [int] NULL,
	[Quantity] [decimal](11, 3) NULL,
	[IsActive] [bit] NULL,
 CONSTRAINT [PK__BOM__3214EC275765CE65] PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[cache]    Script Date: 4/9/2026 10:56:22 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[cache](
	[key] [nvarchar](255) NOT NULL,
	[value] [nvarchar](max) NOT NULL,
	[expiration] [int] NOT NULL,
 CONSTRAINT [cache_key_primary] PRIMARY KEY CLUSTERED 
(
	[key] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[cache_locks]    Script Date: 4/9/2026 10:56:22 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[cache_locks](
	[key] [nvarchar](255) NOT NULL,
	[owner] [nvarchar](255) NOT NULL,
	[expiration] [int] NOT NULL,
 CONSTRAINT [cache_locks_key_primary] PRIMARY KEY CLUSTERED 
(
	[key] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[card_design]    Script Date: 4/9/2026 10:56:22 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[card_design](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[cloud_id] [int] NULL,
	[card_width] [int] NULL,
	[card_height] [int] NULL,
	[image_height] [int] NULL,
	[padding] [int] NULL,
	[no_columns] [int] NULL,
	[no_rows] [int] NULL,
 CONSTRAINT [PK__card_des__3213E83F711AD2D3] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[chat_threads]    Script Date: 4/9/2026 10:56:22 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[chat_threads](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[assistant_id] [nvarchar](255) NOT NULL,
	[thread_id] [nvarchar](255) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[user_id] [bigint] NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__chat_thr__3213E83FF4519569] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[city]    Script Date: 4/9/2026 10:56:22 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[city](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[Gover_ID] [int] NULL,
	[City] [varchar](255) NULL,
 CONSTRAINT [PK__city__3214EC2667FBA018] PRIMARY KEY NONCLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = OFF, ALLOW_PAGE_LOCKS = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[client_visits]    Script Date: 4/9/2026 10:56:23 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[client_visits](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[user_id] [int] NULL,
	[ip_address] [varchar](255) NULL,
	[browser] [varchar](255) NULL,
	[user_agent] [varchar](255) NULL,
	[url] [varchar](255) NULL,
	[method] [varchar](255) NULL,
	[visited_at] [datetime] NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[Clients]    Script Date: 4/9/2026 10:56:23 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Clients](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[name] [varchar](255) NULL,
	[PhoneNo] [varchar](255) NULL,
	[email] [varchar](255) NULL,
	[ReferralBalance] [int] NULL,
	[password] [nvarchar](255) NULL,
	[Cloud_ID] [varchar](255) NULL,
	[IsDist] [bit] NULL,
	[email_verified_at] [datetime] NULL,
	[avatar_url] [varchar](255) NULL,
	[remember_token] [varchar](255) NULL,
	[Address] [varchar](255) NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__Clients__3213E83E02234689] PRIMARY KEY NONCLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = OFF, ALLOW_PAGE_LOCKS = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[collecting_history]    Script Date: 4/9/2026 10:56:23 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[collecting_history](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[M_From] [varchar](255) NULL,
	[M_To] [varchar](255) NULL,
	[Money] [int] NULL,
	[Username] [nvarchar](255) NULL,
	[Date] [date] NULL,
	[Reason] [nvarchar](255) NULL,
	[Type] [nvarchar](255) NULL,
	[Approved_Time] [datetime] NULL,
	[Approved] [bit] NULL,
 CONSTRAINT [PK__collecti__3214EC260F5A9178] PRIMARY KEY NONCLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = OFF, ALLOW_PAGE_LOCKS = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[custom_responses]    Script Date: 4/9/2026 10:56:23 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[custom_responses](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[content] [nvarchar](max) NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__custom_r__3213E83F339C6260] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[daily_balance]    Script Date: 4/9/2026 10:56:23 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[daily_balance](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[Username] [varchar](255) NULL,
	[Amount] [varchar](255) NULL,
	[Date] [date] NULL,
 CONSTRAINT [PK__daily_ba__3214EC27A95DB2E3] PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[daily_stock]    Script Date: 4/9/2026 10:56:23 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[daily_stock](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[Barcode] [int] NULL,
	[Quantity] [decimal](18, 2) NULL,
	[Inventory] [varchar](255) NULL,
	[Date] [date] NULL,
 CONSTRAINT [PK__daily_st__3214EC271281E794] PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[delivery_costs]    Script Date: 4/9/2026 10:56:23 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[delivery_costs](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[government_id] [bigint] NOT NULL,
	[delivery_cost] [decimal](8, 2) NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__delivery__3213E83F2BE05A53] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[event_histories]    Script Date: 4/9/2026 10:56:23 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[event_histories](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[event_id] [bigint] NULL,
	[user_id] [bigint] NULL,
	[action] [varchar](255) NULL,
	[note] [varchar](255) NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__event_hi__3213E83F64E5911D] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[events]    Script Date: 4/9/2026 10:56:23 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[events](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[title] [nvarchar](255) NOT NULL,
	[type] [nvarchar](20) NOT NULL,
	[customer_name] [nvarchar](255) NOT NULL,
	[customer_phone] [nvarchar](32) NOT NULL,
	[details] [nvarchar](max) NULL,
	[user_id] [bigint] NOT NULL,
	[start_at] [datetime] NOT NULL,
	[end_at] [datetime] NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
	[color] [varchar](255) NULL,
	[status] [tinyint] NULL,
	[address] [varchar](255) NULL,
	[location] [varchar](max) NULL,
	[created_by] [int] NULL,
 CONSTRAINT [PK__events__3213E83F542C939C] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[expense_histories]    Script Date: 4/9/2026 10:56:23 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[expense_histories](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[description] [nvarchar](255) NOT NULL,
	[amount] [decimal](10, 2) NOT NULL,
	[date] [date] NOT NULL,
	[created_by] [bigint] NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
	[category] [varchar](255) NULL,
	[invoice_number] [int] NULL,
 CONSTRAINT [PK__expense___3213E83F4B122C10] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[exports]    Script Date: 4/9/2026 10:56:23 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[exports](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[completed_at] [datetime] NULL,
	[file_disk] [nvarchar](255) NOT NULL,
	[file_name] [nvarchar](255) NULL,
	[exporter] [nvarchar](255) NOT NULL,
	[processed_rows] [int] NOT NULL,
	[total_rows] [int] NOT NULL,
	[successful_rows] [int] NOT NULL,
	[user_id] [bigint] NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__exports__3213E83F086C3FFF] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[failed_import_rows]    Script Date: 4/9/2026 10:56:23 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[failed_import_rows](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[data] [nvarchar](max) NOT NULL,
	[import_id] [bigint] NOT NULL,
	[validation_error] [nvarchar](max) NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__failed_i__3213E83F121EB0D1] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[failed_jobs]    Script Date: 4/9/2026 10:56:23 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[failed_jobs](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[uuid] [nvarchar](255) NOT NULL,
	[connection] [nvarchar](max) NOT NULL,
	[queue] [nvarchar](max) NOT NULL,
	[payload] [nvarchar](max) NOT NULL,
	[exception] [nvarchar](max) NOT NULL,
	[failed_at] [datetime] NOT NULL,
 CONSTRAINT [PK__failed_j__3213E83F947A911E] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[fblog_categories]    Script Date: 4/9/2026 10:56:23 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[fblog_categories](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](155) NOT NULL,
	[slug] [nvarchar](155) NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__fblog_ca__3213E83FA718414F] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[fblog_category_fblog_post]    Script Date: 4/9/2026 10:56:23 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[fblog_category_fblog_post](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[post_id] [bigint] NOT NULL,
	[category_id] [bigint] NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__fblog_ca__3213E83FD4A84B92] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[fblog_comments]    Script Date: 4/9/2026 10:56:23 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[fblog_comments](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[user_id] [bigint] NOT NULL,
	[post_id] [bigint] NOT NULL,
	[comment] [nvarchar](max) NOT NULL,
	[approved] [bit] NOT NULL,
	[approved_at] [datetime] NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__fblog_co__3213E83FDB942D34] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[fblog_news_letters]    Script Date: 4/9/2026 10:56:23 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[fblog_news_letters](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[email] [nvarchar](100) NOT NULL,
	[subscribed] [bit] NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__fblog_ne__3213E83F8380BB8C] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[fblog_post_fblog_tag]    Script Date: 4/9/2026 10:56:23 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[fblog_post_fblog_tag](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[post_id] [bigint] NOT NULL,
	[tag_id] [bigint] NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__fblog_po__3213E83FF572F9B5] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[fblog_posts]    Script Date: 4/9/2026 10:56:23 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[fblog_posts](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[title] [nvarchar](255) NOT NULL,
	[slug] [nvarchar](255) NOT NULL,
	[sub_title] [nvarchar](255) NULL,
	[body] [nvarchar](max) NOT NULL,
	[status] [nvarchar](255) NOT NULL,
	[published_at] [datetime] NULL,
	[scheduled_for] [datetime] NULL,
	[cover_photo_path] [nvarchar](255) NOT NULL,
	[photo_alt_text] [nvarchar](255) NOT NULL,
	[user_id] [bigint] NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__fblog_po__3213E83FE03DB473] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[fblog_seo_details]    Script Date: 4/9/2026 10:56:23 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[fblog_seo_details](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[post_id] [bigint] NOT NULL,
	[title] [nvarchar](255) NOT NULL,
	[keywords] [nvarchar](max) NULL,
	[description] [nvarchar](max) NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__fblog_se__3213E83F2FFEE785] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[fblog_settings]    Script Date: 4/9/2026 10:56:23 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[fblog_settings](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[title] [nvarchar](155) NULL,
	[description] [nvarchar](max) NULL,
	[logo] [nvarchar](255) NULL,
	[favicon] [nvarchar](255) NULL,
	[organization_name] [nvarchar](255) NULL,
	[google_console_code] [nvarchar](255) NULL,
	[google_analytic_code] [nvarchar](max) NULL,
	[google_adsense_code] [nvarchar](255) NULL,
	[quick_links] [nvarchar](max) NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__fblog_se__3213E83F34F7ECC3] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[fblog_share_snippets]    Script Date: 4/9/2026 10:56:23 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[fblog_share_snippets](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[script_code] [nvarchar](max) NOT NULL,
	[html_code] [nvarchar](max) NOT NULL,
	[active] [bit] NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__fblog_sh__3213E83F4DA0543A] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[fblog_tags]    Script Date: 4/9/2026 10:56:23 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[fblog_tags](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](50) NOT NULL,
	[slug] [nvarchar](155) NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__fblog_ta__3213E83F03A5994E] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[filachat_agents]    Script Date: 4/9/2026 10:56:23 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[filachat_agents](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[agentable_id] [bigint] NOT NULL,
	[agentable_type] [nvarchar](255) NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__filachat__3213E83F5E6B5CD2] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[filachat_conversations]    Script Date: 4/9/2026 10:56:24 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[filachat_conversations](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[senderable_id] [bigint] NOT NULL,
	[senderable_type] [nvarchar](255) NOT NULL,
	[receiverable_id] [bigint] NOT NULL,
	[receiverable_type] [nvarchar](255) NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__filachat__3213E83F426E2A53] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[filachat_messages]    Script Date: 4/9/2026 10:56:24 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[filachat_messages](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[filachat_conversation_id] [bigint] NOT NULL,
	[message] [nvarchar](max) NULL,
	[attachments] [nvarchar](max) NULL,
	[original_attachment_file_names] [nvarchar](max) NULL,
	[reactions] [nvarchar](max) NULL,
	[is_starred] [bit] NOT NULL,
	[metadata] [nvarchar](max) NULL,
	[reply_to_message_id] [bigint] NULL,
	[senderable_id] [bigint] NOT NULL,
	[senderable_type] [nvarchar](255) NOT NULL,
	[receiverable_id] [bigint] NOT NULL,
	[receiverable_type] [nvarchar](255) NOT NULL,
	[last_read_at] [datetime] NULL,
	[edited_at] [datetime] NULL,
	[sender_deleted_at] [datetime] NULL,
	[receiver_deleted_at] [datetime] NULL,
	[deleted_at] [datetime] NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__filachat__3213E83FD76D081A] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[filament_webhook_server_histories]    Script Date: 4/9/2026 10:56:24 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[filament_webhook_server_histories](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[webhook_client] [bigint] NULL,
	[uuid] [uniqueidentifier] NOT NULL,
	[status_code] [nvarchar](255) NULL,
	[errorMessage] [nvarchar](max) NULL,
	[errorType] [nvarchar](255) NULL,
	[attempt] [int] NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__filament__3213E83F1C303CB9] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[filament_webhook_servers]    Script Date: 4/9/2026 10:56:24 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[filament_webhook_servers](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[description] [nvarchar](max) NOT NULL,
	[url] [nvarchar](255) NOT NULL,
	[method] [nvarchar](255) NOT NULL,
	[model] [nvarchar](255) NOT NULL,
	[header] [nvarchar](max) NULL,
	[data_option] [nvarchar](255) NOT NULL,
	[verifySsl] [bit] NOT NULL,
	[status] [nvarchar](255) NULL,
	[events] [nvarchar](max) NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__filament__3213E83F4892323B] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[governorate]    Script Date: 4/9/2026 10:56:24 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[governorate](
	[ID] [int] NOT NULL,
	[Governorate] [varchar](255) NULL,
	[Country] [nvarchar](255) NULL,
 CONSTRAINT [PK__governor__3214EC26FB115CC0] PRIMARY KEY NONCLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = OFF, ALLOW_PAGE_LOCKS = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[health_check_result_history_items]    Script Date: 4/9/2026 10:56:24 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[health_check_result_history_items](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[check_name] [nvarchar](255) NOT NULL,
	[check_label] [nvarchar](255) NOT NULL,
	[status] [nvarchar](255) NOT NULL,
	[notification_message] [nvarchar](max) NULL,
	[short_summary] [nvarchar](255) NULL,
	[meta] [nvarchar](max) NOT NULL,
	[ended_at] [datetime] NOT NULL,
	[batch] [uniqueidentifier] NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__health_c__3213E83F74208002] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[imports]    Script Date: 4/9/2026 10:56:24 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[imports](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[completed_at] [datetime] NULL,
	[file_name] [nvarchar](255) NOT NULL,
	[file_path] [nvarchar](255) NOT NULL,
	[importer] [nvarchar](255) NOT NULL,
	[processed_rows] [int] NOT NULL,
	[total_rows] [int] NOT NULL,
	[successful_rows] [int] NOT NULL,
	[user_id] [bigint] NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__imports__3213E83FB8598394] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[Invoices]    Script Date: 4/9/2026 10:56:24 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Invoices](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[LicenseID] [int] NULL,
	[ClientID] [int] NULL,
	[Invoice_No] [int] NULL,
	[Item_Name] [varchar](255) NULL,
	[QTY] [int] NULL,
	[Price] [int] NULL,
	[Amount] [int] NULL,
	[InvoiceDate] [date] NULL,
	[DueDate] [date] NULL,
	[Voucher_ID] [int] NULL,
	[Username] [nvarchar](255) NULL,
	[Type] [varchar](255) NULL,
	[Description] [varchar](255) NULL,
 CONSTRAINT [PK__Invoices__3214EC26EF7E8C06] PRIMARY KEY NONCLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = OFF, ALLOW_PAGE_LOCKS = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[item_master]    Script Date: 4/9/2026 10:56:24 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[item_master](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[Code] [int] NULL,
	[Item_Name] [nvarchar](255) NULL,
	[Price] [int] NULL,
	[IsProduct] [bit] NULL,
	[IsSales] [bit] NULL,
	[IsActive] [bit] NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
	[IsStock] [bit] NULL,
 CONSTRAINT [PK__item_mas__3214EC264554FADF] PRIMARY KEY NONCLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = OFF, ALLOW_PAGE_LOCKS = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[job_batches]    Script Date: 4/9/2026 10:56:24 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[job_batches](
	[id] [nvarchar](255) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[total_jobs] [int] NOT NULL,
	[pending_jobs] [int] NOT NULL,
	[failed_jobs] [int] NOT NULL,
	[failed_job_ids] [nvarchar](max) NOT NULL,
	[options] [nvarchar](max) NULL,
	[cancelled_at] [int] NULL,
	[created_at] [int] NOT NULL,
	[finished_at] [int] NULL,
 CONSTRAINT [job_batches_id_primary] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[jobs]    Script Date: 4/9/2026 10:56:24 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[jobs](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[queue] [nvarchar](255) NOT NULL,
	[payload] [nvarchar](max) NOT NULL,
	[attempts] [tinyint] NOT NULL,
	[reserved_at] [int] NULL,
	[available_at] [int] NOT NULL,
	[created_at] [int] NOT NULL,
 CONSTRAINT [PK__jobs__3213E83FAA99D3C9] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[Keys]    Script Date: 4/9/2026 10:56:24 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Keys](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[License_ID] [int] NULL,
	[Computer_ID] [varchar](255) NULL,
	[License_Key] [varchar](255) NULL,
	[Bios_ID] [varchar](255) NULL,
	[Disk_ID] [varchar](255) NULL,
	[Base_ID] [varchar](255) NULL,
	[Video_ID] [varchar](255) NULL,
	[Mac_ID] [varchar](255) NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
	[device_name] [varchar](255) NULL,
	[is_main] [tinyint] NULL,
 CONSTRAINT [PK__Keys__3214EC26606B5531] PRIMARY KEY NONCLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = OFF, ALLOW_PAGE_LOCKS = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[lead_interactions]    Script Date: 4/9/2026 10:56:24 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[lead_interactions](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[lead_id] [int] NOT NULL,
	[user_id] [bigint] NULL,
	[type] [varchar](50) NULL,
	[subject] [varchar](255) NULL,
	[notes] [nvarchar](max) NOT NULL,
	[interaction_date] [datetime] NULL,
	[outcome] [varchar](255) NULL,
	[next_action] [varchar](255) NULL,
	[follow_up_date] [datetime] NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[leads]    Script Date: 4/9/2026 10:56:24 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[leads](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[phone] [nvarchar](20) NOT NULL,
	[email] [nvarchar](255) NULL,
	[service_type] [nvarchar](255) NULL,
	[source] [nvarchar](50) NOT NULL,
	[notes] [ntext] NULL,
	[status] [nvarchar](50) NULL,
	[assigned_to] [bigint] NULL,
	[created_by] [bigint] NULL,
	[created_at] [datetime2](7) NULL,
	[updated_at] [datetime2](7) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY],
UNIQUE NONCLUSTERED 
(
	[phone] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[Licenses]    Script Date: 4/9/2026 10:56:24 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Licenses](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[Company_Name] [varchar](255) NULL,
	[ProductID] [int] NULL,
	[ClientID] [int] NULL,
	[GoverID] [int] NULL,
	[CityID] [int] NULL,
	[Address] [varchar](255) NULL,
	[LicenseType] [varchar](255) NULL,
	[Period] [int] NULL,
	[StartDate] [date] NULL,
	[EndDate] [date] NULL,
	[Cost] [int] NULL,
	[Paid] [int] NULL,
	[Remain] [int] NULL,
	[PayStatus] [bit] NULL,
	[SupportBalance] [int] NULL,
	[Application_Version] [varchar](255) NULL,
	[Approved_By] [varchar](255) NULL,
	[LastOnline] [datetime] NULL,
	[IsActive] [bit] NULL,
	[Edition_ID] [int] NULL,
	[Server_IP] [varchar](255) NULL,
 CONSTRAINT [PK__Licenses__3214EC26163A9A9B] PRIMARY KEY NONCLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = OFF, ALLOW_PAGE_LOCKS = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[Licenses_Online]    Script Date: 4/9/2026 10:56:24 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Licenses_Online](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[LicensesID] [int] NULL,
	[LastOnline] [datetime] NULL,
 CONSTRAINT [PK__Licenses__3214EC27EE83C979] PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[Logging]    Script Date: 4/9/2026 10:56:24 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Logging](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[Device_Key] [nvarchar](255) NULL,
	[Date] [datetime] NULL,
	[EID] [int] NULL,
	[Status] [bit] NULL,
	[Message] [varchar](max) NULL,
	[Trace] [varchar](max) NULL,
	[Form_Name] [varchar](max) NULL,
	[ImageURL] [varchar](max) NULL,
	[Checked_By] [varchar](255) NULL,
	[App_Version] [varchar](255) NULL,
 CONSTRAINT [PK__Logging__3214EC26B6A06125] PRIMARY KEY NONCLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = OFF, ALLOW_PAGE_LOCKS = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[Mail]    Script Date: 4/9/2026 10:56:24 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Mail](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[License_ID] [int] NULL,
	[Start_Date] [date] NULL,
	[End_Date] [date] NULL,
	[IsActive] [bit] NULL,
 CONSTRAINT [PK__Mail__3214EC269F3FAA1C] PRIMARY KEY NONCLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = OFF, ALLOW_PAGE_LOCKS = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[mail_logs]    Script Date: 4/9/2026 10:56:24 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[mail_logs](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[from] [nvarchar](255) NULL,
	[to] [nvarchar](255) NULL,
	[cc] [nvarchar](255) NULL,
	[bcc] [nvarchar](255) NULL,
	[subject] [nvarchar](255) NOT NULL,
	[body] [nvarchar](max) NOT NULL,
	[headers] [nvarchar](max) NULL,
	[attachments] [nvarchar](max) NULL,
	[message_id] [uniqueidentifier] NULL,
	[status] [nvarchar](255) NULL,
	[data] [nvarchar](max) NULL,
	[opened] [datetime] NULL,
	[delivered] [datetime] NULL,
	[complaint] [datetime] NULL,
	[bounced] [datetime] NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__mail_log__3213E83FE965E126] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[MailConfig]    Script Date: 4/9/2026 10:56:24 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[MailConfig](
	[ID] [int] NOT NULL,
	[Description] [nvarchar](255) NULL,
	[Value] [nvarchar](255) NULL,
 CONSTRAINT [PK__MailConf__3214EC2661099F91] PRIMARY KEY NONCLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = OFF, ALLOW_PAGE_LOCKS = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[migrations]    Script Date: 4/9/2026 10:56:24 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[migrations](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[migration] [nvarchar](255) NOT NULL,
	[batch] [int] NOT NULL,
 CONSTRAINT [PK__migratio__3213E83FA2D70E73] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[model_has_permissions]    Script Date: 4/9/2026 10:56:24 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[model_has_permissions](
	[permission_id] [bigint] NOT NULL,
	[model_type] [nvarchar](255) NOT NULL,
	[model_id] [bigint] NOT NULL,
 CONSTRAINT [model_has_permissions_permission_model_type_primary] PRIMARY KEY CLUSTERED 
(
	[permission_id] ASC,
	[model_id] ASC,
	[model_type] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[model_has_roles]    Script Date: 4/9/2026 10:56:24 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[model_has_roles](
	[role_id] [bigint] NOT NULL,
	[model_type] [nvarchar](255) NOT NULL,
	[model_id] [bigint] NOT NULL,
 CONSTRAINT [model_has_roles_role_model_type_primary] PRIMARY KEY CLUSTERED 
(
	[role_id] ASC,
	[model_id] ASC,
	[model_type] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[monitored_scheduled_task_log_items]    Script Date: 4/9/2026 10:56:25 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[monitored_scheduled_task_log_items](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[monitored_scheduled_task_id] [bigint] NOT NULL,
	[type] [nvarchar](255) NOT NULL,
	[meta] [nvarchar](max) NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__monitore__3213E83F916A4456] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[monitored_scheduled_tasks]    Script Date: 4/9/2026 10:56:25 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[monitored_scheduled_tasks](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[type] [nvarchar](255) NULL,
	[cron_expression] [nvarchar](255) NOT NULL,
	[timezone] [nvarchar](255) NULL,
	[ping_url] [nvarchar](255) NULL,
	[last_started_at] [datetime] NULL,
	[last_finished_at] [datetime] NULL,
	[last_failed_at] [datetime] NULL,
	[last_skipped_at] [datetime] NULL,
	[registered_on_oh_dear_at] [datetime] NULL,
	[last_pinged_at] [datetime] NULL,
	[grace_time_in_minutes] [int] NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__monitore__3213E83FD980BF98] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[note_metas]    Script Date: 4/9/2026 10:56:25 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[note_metas](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[model_id] [bigint] NULL,
	[model_type] [nvarchar](255) NULL,
	[note_id] [bigint] NOT NULL,
	[key] [nvarchar](255) NOT NULL,
	[value] [nvarchar](max) NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__note_met__3213E83F53C5B9FC] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[notes]    Script Date: 4/9/2026 10:56:25 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[notes](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[group] [nvarchar](255) NULL,
	[status] [nvarchar](255) NULL,
	[model_id] [bigint] NULL,
	[model_type] [nvarchar](255) NULL,
	[user_id] [bigint] NULL,
	[user_type] [nvarchar](255) NULL,
	[title] [nvarchar](255) NULL,
	[body] [nvarchar](max) NULL,
	[background] [nvarchar](255) NULL,
	[border] [nvarchar](255) NULL,
	[color] [nvarchar](255) NULL,
	[checklist] [nvarchar](max) NULL,
	[icon] [nvarchar](255) NULL,
	[font_size] [nvarchar](255) NULL,
	[font] [nvarchar](255) NULL,
	[date] [date] NULL,
	[time] [time](7) NULL,
	[is_public] [bit] NULL,
	[is_pined] [bit] NULL,
	[order] [int] NULL,
	[place_in] [nvarchar](255) NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__notes__3213E83F56712063] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[notifications]    Script Date: 4/9/2026 10:56:25 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[notifications](
	[id] [uniqueidentifier] NOT NULL,
	[type] [nvarchar](255) NOT NULL,
	[notifiable_type] [nvarchar](255) NOT NULL,
	[notifiable_id] [bigint] NOT NULL,
	[data] [nvarchar](max) NOT NULL,
	[read_at] [datetime] NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [notifications_id_primary] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[notifications_logs]    Script Date: 4/9/2026 10:56:25 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[notifications_logs](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[model_type] [nvarchar](255) NULL,
	[model_id] [bigint] NULL,
	[title] [nvarchar](max) NOT NULL,
	[description] [nvarchar](max) NULL,
	[type] [nvarchar](255) NOT NULL,
	[provider] [nvarchar](255) NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__notifica__3213E83FF49903DC] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[notifications_templates]    Script Date: 4/9/2026 10:56:25 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[notifications_templates](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[key] [nvarchar](255) NOT NULL,
	[title] [nvarchar](255) NOT NULL,
	[body] [nvarchar](max) NULL,
	[url] [nvarchar](255) NULL,
	[icon] [nvarchar](255) NULL,
	[type] [nvarchar](255) NULL,
	[providers] [nvarchar](max) NULL,
	[action] [nvarchar](255) NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__notifica__3213E83F8138A612] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[order_history]    Script Date: 4/9/2026 10:56:25 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[order_history](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[order_id] [int] NULL,
	[comment] [varchar](255) NULL,
	[created_by] [varchar](255) NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__order_hi__3213E83F5C4D3B5C] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[order_items]    Script Date: 4/9/2026 10:56:25 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[order_items](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[order_id] [int] NULL,
	[item_type] [int] NULL,
	[item_id] [int] NULL,
	[qty] [int] NULL,
	[unit_price] [decimal](11, 2) NULL,
	[amount] [decimal](11, 2) NULL,
	[created_by] [varchar](255) NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__order_it__3213E83FE20DE99C] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[order_type]    Script Date: 4/9/2026 10:56:25 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[order_type](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[type_id] [int] NOT NULL,
	[type_name] [nvarchar](255) NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__order_ty__3213E83F5A96361C] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[orders]    Script Date: 4/9/2026 10:56:25 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[orders](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[order_type] [int] NULL,
	[title] [varchar](255) NULL,
	[customer_id] [int] NULL,
	[assign_user_id] [int] NULL,
	[created_by] [int] NULL,
	[request_date] [datetime] NULL,
	[status] [int] NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__orders__3213E83F9577CD46] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[password_reset_tokens]    Script Date: 4/9/2026 10:56:25 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[password_reset_tokens](
	[email] [nvarchar](255) NOT NULL,
	[token] [nvarchar](255) NOT NULL,
	[created_at] [datetime] NULL,
 CONSTRAINT [password_reset_tokens_email_primary] PRIMARY KEY CLUSTERED 
(
	[email] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[password_resets]    Script Date: 4/9/2026 10:56:25 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[password_resets](
	[email] [nvarchar](255) NOT NULL,
	[token] [nvarchar](255) NOT NULL,
	[created_at] [datetime] NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[payment_histories]    Script Date: 4/9/2026 10:56:25 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[payment_histories](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[client_id] [bigint] NOT NULL,
	[from_number] [nvarchar](255) NOT NULL,
	[to_number] [nvarchar](255) NOT NULL,
	[amount] [decimal](10, 2) NOT NULL,
	[type] [nvarchar](255) NOT NULL,
	[transaction_date] [datetime] NOT NULL,
	[status] [bigint] NOT NULL,
	[approved_by] [bigint] NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__payment___3213E83F492DE32B] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[Payment_Method]    Script Date: 4/9/2026 10:56:25 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Payment_Method](
	[ID] [int] NOT NULL,
	[Type] [varchar](255) NULL,
	[Phoneno] [varchar](255) NULL,
	[Email] [varchar](255) NULL,
	[IsActive] [bit] NULL,
 CONSTRAINT [PK__Payment___3214EC273C798113] PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[permissions]    Script Date: 4/9/2026 10:56:25 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[permissions](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[guard_name] [nvarchar](255) NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__permissi__3213E83F6B95748B] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[personal_access_tokens]    Script Date: 4/9/2026 10:56:25 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[personal_access_tokens](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[tokenable_type] [nvarchar](255) NOT NULL,
	[tokenable_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[token] [nvarchar](64) NOT NULL,
	[abilities] [nvarchar](max) NULL,
	[last_used_at] [datetime] NULL,
	[expires_at] [datetime] NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__personal__3213E83FB3E14422] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[price_quotes]    Script Date: 4/9/2026 10:56:25 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[price_quotes](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[quote_number] [nvarchar](50) NOT NULL,
	[client_name] [nvarchar](255) NOT NULL,
	[client_phone] [nvarchar](20) NOT NULL,
	[client_email] [nvarchar](255) NULL,
	[subtotal] [decimal](10, 2) NULL,
	[discount] [decimal](10, 2) NULL,
	[discount_type] [nvarchar](20) NULL,
	[total] [decimal](10, 2) NULL,
	[expiry_date] [date] NOT NULL,
	[notes] [ntext] NULL,
	[status] [nvarchar](50) NULL,
	[created_by] [int] NULL,
	[created_at] [datetime2](7) NULL,
	[updated_at] [datetime2](7) NULL,
	[lead_id] [int] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY],
UNIQUE NONCLUSTERED 
(
	[quote_number] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[product_editions]    Script Date: 4/9/2026 10:56:25 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[product_editions](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[product_id] [int] NULL,
	[edition] [varchar](255) NULL,
	[cost] [money] NULL,
	[price] [money] NULL,
	[devices] [int] NULL,
 CONSTRAINT [PK__product___3213E83FF10BB566] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[product_rent]    Script Date: 4/9/2026 10:56:25 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[product_rent](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[product_id] [int] NULL,
	[monthly] [int] NULL,
	[yearly] [int] NULL,
 CONSTRAINT [PK__product___3213E83FA4DB59BB] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[Products]    Script Date: 4/9/2026 10:56:25 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Products](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[Product_Name] [varchar](255) NULL,
	[Description] [varchar](255) NULL,
	[Version_Number] [varchar](255) NULL,
	[License_Cost] [int] NULL,
	[Edition] [varchar](255) NULL,
	[Price] [int] NULL,
	[devices] [int] NULL,
	[installation] [int] NULL,
 CONSTRAINT [PK__Products__3214EC265A97A40F] PRIMARY KEY NONCLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = OFF, ALLOW_PAGE_LOCKS = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[ProductsFeatures]    Script Date: 4/9/2026 10:56:25 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[ProductsFeatures](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[ProductID] [int] NULL,
	[FeatureName] [varchar](255) NULL,
	[FeatureDescription] [varchar](255) NULL,
	[FeatureAmount] [int] NULL,
 CONSTRAINT [PK__Products__3214EC26B779681D] PRIMARY KEY NONCLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = OFF, ALLOW_PAGE_LOCKS = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[purchasing]    Script Date: 4/9/2026 10:56:25 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[purchasing](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[InvoiceNo] [varchar](255) NULL,
	[Barcode] [int] NULL,
	[Quantity] [int] NULL,
	[Price] [float] NULL,
	[INV_Date] [date] NULL,
	[INV_Time] [time](7) NULL,
	[Username] [nvarchar](255) NULL,
	[Amount] [float] NULL,
 CONSTRAINT [PK__purchasi__3214EC265CF78B7D] PRIMARY KEY NONCLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = OFF, ALLOW_PAGE_LOCKS = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[queue_monitors]    Script Date: 4/9/2026 10:56:25 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[queue_monitors](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[job_id] [nvarchar](255) NOT NULL,
	[name] [nvarchar](255) NULL,
	[queue] [nvarchar](255) NULL,
	[started_at] [datetime] NULL,
	[finished_at] [datetime] NULL,
	[failed] [bit] NOT NULL,
	[attempt] [int] NOT NULL,
	[progress] [int] NULL,
	[exception_message] [nvarchar](max) NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__queue_mo__3213E83F8A34ACA6] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[quote_line_items]    Script Date: 4/9/2026 10:56:26 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[quote_line_items](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[quote_id] [int] NOT NULL,
	[item_id] [int] NULL,
	[item_name] [nvarchar](255) NOT NULL,
	[quantity] [int] NOT NULL,
	[unit_price] [decimal](10, 2) NOT NULL,
	[line_total] [decimal](10, 2) NOT NULL,
	[created_at] [datetime2](7) NULL,
	[updated_at] [datetime2](7) NULL,
	[item_type] [nvarchar](50) NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[Referrals]    Script Date: 4/9/2026 10:56:26 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Referrals](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[ReferrerID] [int] NULL,
	[ReferredID] [int] NULL,
	[Date] [datetime2](7) NULL,
	[Status] [bit] NULL,
 CONSTRAINT [PK__Referral__3214EC261362359B] PRIMARY KEY NONCLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = OFF, ALLOW_PAGE_LOCKS = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[Remote]    Script Date: 4/9/2026 10:56:26 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Remote](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[license_id] [int] NULL,
	[Anydesk] [int] NULL,
	[Rustdesk] [varchar](25) NULL,
	[Teamviewer] [int] NULL,
	[Remark] [nvarchar](255) NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__Remote__3214EC26A2A910BB] PRIMARY KEY NONCLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = OFF, ALLOW_PAGE_LOCKS = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[Remote_Sub]    Script Date: 4/9/2026 10:56:26 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Remote_Sub](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[License_ID] [int] NULL,
	[Start_Date] [date] NULL,
	[End_Date] [date] NULL,
	[IsActive] [bit] NULL,
PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[role_has_permissions]    Script Date: 4/9/2026 10:56:26 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[role_has_permissions](
	[permission_id] [bigint] NOT NULL,
	[role_id] [bigint] NOT NULL,
 CONSTRAINT [role_has_permissions_permission_id_role_id_primary] PRIMARY KEY CLUSTERED 
(
	[permission_id] ASC,
	[role_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[roles]    Script Date: 4/9/2026 10:56:26 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[roles](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[guard_name] [nvarchar](255) NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__roles__3213E83FDE5EA22F] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[sales_target]    Script Date: 4/9/2026 10:56:26 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[sales_target](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[Type] [varchar](255) NULL,
	[Type_ID] [int] NULL,
	[Target] [int] NULL,
	[Achieved] [int] NULL,
	[Remain]  AS ([Achieved]-[Target]),
	[Username] [varchar](255) NULL,
	[Target_Month] [varchar](255) NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__sales_ta__3214EC27D8612DC4] PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[sessions]    Script Date: 4/9/2026 10:56:26 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[sessions](
	[id] [nvarchar](255) NOT NULL,
	[user_id] [bigint] NULL,
	[ip_address] [nvarchar](45) NULL,
	[user_agent] [nvarchar](max) NULL,
	[payload] [nvarchar](max) NOT NULL,
	[last_activity] [int] NOT NULL,
 CONSTRAINT [sessions_id_primary] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[settings]    Script Date: 4/9/2026 10:56:26 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[settings](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[group] [nvarchar](255) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[locked] [bit] NOT NULL,
	[payload] [nvarchar](max) NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__settings__3213E83FAB640DCE] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[shop_order_items]    Script Date: 4/9/2026 10:56:26 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[shop_order_items](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[shop_order_id] [int] NULL,
	[item_id] [int] NULL,
	[qty] [int] NOT NULL,
	[unit_price] [decimal](10, 2) NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__shop_ord__3213E83F047D645D] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[shop_orders]    Script Date: 4/9/2026 10:56:26 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[shop_orders](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[client_id] [int] NULL,
	[number] [nvarchar](32) NOT NULL,
	[item_amount] [int] NOT NULL,
	[shipping_price] [int] NULL,
	[total_amount] [int] NOT NULL,
	[status] [nvarchar](255) NOT NULL,
	[order_type] [int] NULL,
	[return_reason] [nvarchar](255) NOT NULL,
	[delay_reason] [nvarchar](255) NOT NULL,
	[captain_name] [nvarchar](255) NOT NULL,
	[captain_number1] [nvarchar](255) NOT NULL,
	[notes] [nvarchar](max) NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
	[deleted_at] [datetime] NULL,
 CONSTRAINT [PK__shop_ord__3213E83F8417C20E] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[stock]    Script Date: 4/9/2026 10:56:26 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[stock](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[Barcode] [int] NULL,
	[Quantity] [decimal](11, 3) NULL,
	[Price] [real] NULL,
	[Amount] [real] NULL,
	[Inventory] [nvarchar](255) NULL,
 CONSTRAINT [PK__stock__3213E83E2E633E95] PRIMARY KEY NONCLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = OFF, ALLOW_PAGE_LOCKS = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[stock_transaction]    Script Date: 4/9/2026 10:56:26 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[stock_transaction](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[Barcode] [varchar](255) NULL,
	[Transaction_Type] [varchar](255) NULL,
	[Quantity] [decimal](18, 2) NULL,
	[Inventory] [varchar](255) NULL,
	[Transaction_Date] [datetime] NULL,
	[Reference] [varchar](255) NULL,
 CONSTRAINT [PK__stock_tr__3214EC270816F843] PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[tag_tickets]    Script Date: 4/9/2026 10:56:26 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[tag_tickets](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[tag_id] [bigint] NOT NULL,
	[ticket_id] [bigint] NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__tag_tick__3213E83F3313E57D] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[tags]    Script Date: 4/9/2026 10:56:26 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[tags](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[color] [nvarchar](255) NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__tags__3213E83FECFAFA67] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[Technical_Support]    Script Date: 4/9/2026 10:56:26 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Technical_Support](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[LicenseID] [int] NULL,
	[Start_Date] [date] NULL,
	[End_Date] [date] NULL,
	[IsActive] [bit] NULL,
 CONSTRAINT [PK__Technica__3214EC26A8E8C85C] PRIMARY KEY NONCLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = OFF, ALLOW_PAGE_LOCKS = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[template_has_roles]    Script Date: 4/9/2026 10:56:26 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[template_has_roles](
	[template_id] [bigint] NOT NULL,
	[role_id] [bigint] NOT NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[test]    Script Date: 4/9/2026 10:56:26 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[test](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[text] [varchar](255) NULL,
 CONSTRAINT [PK__test__3214EC27AA514014] PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[ticket_clients]    Script Date: 4/9/2026 10:56:26 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[ticket_clients](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[ticket_id] [int] NOT NULL,
	[client_id] [int] NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__ticket_c__3213E83FD73C1B18] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[ticket_events]    Script Date: 4/9/2026 10:56:26 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[ticket_events](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[type] [nvarchar](255) NOT NULL,
	[content] [nvarchar](max) NOT NULL,
	[ticket_id] [int] NOT NULL,
	[user_id] [int] NULL,
	[file] [nvarchar](max) NULL,
	[private] [tinyint] NOT NULL,
	[client_id] [int] NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__ticket_e__3213E83F87656531] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[tickets]    Script Date: 4/9/2026 10:56:26 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[tickets](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[ticket_number] [int] NOT NULL,
	[unread] [tinyint] NOT NULL,
	[unread_user] [tinyint] NOT NULL,
	[title] [nvarchar](255) NOT NULL,
	[content] [nvarchar](max) NOT NULL,
	[file] [nvarchar](255) NULL,
	[closed] [tinyint] NOT NULL,
	[client_id] [int] NOT NULL,
	[user_id] [int] NULL,
	[edited_title] [tinyint] NOT NULL,
	[first_closed_at] [datetime] NULL,
	[last_closed_at] [datetime] NULL,
	[reopened] [tinyint] NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
	[closed_by] [int] NULL,
	[product_id] [int] NULL,
	[license_id] [int] NULL,
 CONSTRAINT [PK__tickets__3213E83F86A906EF] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[Transfer_History]    Script Date: 4/9/2026 10:56:26 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Transfer_History](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[ItemID] [int] NULL,
	[StockID] [int] NULL,
	[QTY] [int] NULL,
	[From_Inventory] [nvarchar](255) NULL,
	[To_Inventory] [nvarchar](255) NULL,
	[T_Date] [date] NULL,
	[Approved] [bit] NULL,
	[A_Time] [nvarchar](255) NULL,
PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[typables]    Script Date: 4/9/2026 10:56:26 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[typables](
	[type_id] [bigint] NOT NULL,
	[typables_id] [bigint] NOT NULL,
	[typables_type] [nvarchar](255) NOT NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[types]    Script Date: 4/9/2026 10:56:27 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[types](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[parent_id] [bigint] NULL,
	[model_type] [nvarchar](255) NULL,
	[model_id] [bigint] NULL,
	[for] [nvarchar](255) NULL,
	[type] [nvarchar](255) NULL,
	[name] [nvarchar](255) NOT NULL,
	[key] [nvarchar](255) NOT NULL,
	[description] [nvarchar](max) NULL,
	[color] [nvarchar](255) NULL,
	[icon] [nvarchar](255) NULL,
	[is_activated] [bit] NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__types__3213E83FE6109EA4] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[types_metas]    Script Date: 4/9/2026 10:56:27 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[types_metas](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[model_id] [bigint] NULL,
	[model_type] [nvarchar](255) NULL,
	[type_id] [bigint] NOT NULL,
	[key] [nvarchar](255) NOT NULL,
	[value] [nvarchar](max) NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__types_me__3213E83F1D6AB91F] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[user_has_notifications]    Script Date: 4/9/2026 10:56:27 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[user_has_notifications](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[model_type] [nvarchar](255) NOT NULL,
	[model_id] [bigint] NOT NULL,
	[provider] [nvarchar](255) NULL,
	[provider_token] [nvarchar](255) NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__user_has__3213E83FA344CFF5] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[user_notifications]    Script Date: 4/9/2026 10:56:27 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[user_notifications](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[model_type] [nvarchar](255) NULL,
	[model_id] [bigint] NULL,
	[template_id] [bigint] NULL,
	[title] [nvarchar](max) NOT NULL,
	[description] [nvarchar](max) NULL,
	[url] [nvarchar](255) NULL,
	[icon] [nvarchar](255) NULL,
	[type] [nvarchar](255) NULL,
	[privacy] [nvarchar](255) NULL,
	[data] [nvarchar](max) NULL,
	[created_by] [bigint] NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__user_not__3213E83F8361A8E7] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[user_read_notifications]    Script Date: 4/9/2026 10:56:27 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[user_read_notifications](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[model_type] [nvarchar](255) NOT NULL,
	[model_id] [bigint] NOT NULL,
	[notification_id] [bigint] NOT NULL,
	[read] [bit] NOT NULL,
	[open] [bit] NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__user_rea__3213E83FC87ADF07] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[user_tickets]    Script Date: 4/9/2026 10:56:27 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[user_tickets](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[ticket_id] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK__user_tic__3213E83FDF4ED8C8] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[users]    Script Date: 4/9/2026 10:56:27 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[users](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[email] [nvarchar](255) NOT NULL,
	[email_verified_at] [datetime] NULL,
	[password] [nvarchar](255) NOT NULL,
	[remember_token] [nvarchar](100) NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
	[avatar_url] [nvarchar](255) NULL,
	[custom_fields] [nvarchar](max) NULL,
	[balance] [int] NULL,
	[PhoneNo] [varchar](255) NULL,
	[Address] [varchar](255) NULL,
 CONSTRAINT [PK__users__3213E83F381C28EA] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[Vouchers]    Script Date: 4/9/2026 10:56:27 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Vouchers](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[inv_id] [int] NOT NULL,
	[cloud_id] [int] NOT NULL,
	[realm_id] [int] NOT NULL,
	[nasidentifier] [nvarchar](255) NOT NULL,
	[qty] [int] NOT NULL,
	[batch] [nvarchar](255) NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
	[never_expire] [varchar](255) NULL,
	[caption] [varchar](255) NULL,
	[profile_id] [int] NULL,
	[days_valid] [int] NULL,
	[hours_valid] [int] NULL,
	[minutes_valid] [int] NULL,
 CONSTRAINT [PK__Vouchers__3213E83F9BD4A7B6] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[VPrice]    Script Date: 4/9/2026 10:56:27 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[VPrice](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[Description] [varchar](255) NULL,
	[Type] [varchar](255) NULL,
	[QTY] [int] NULL,
	[Amount] [int] NULL,
	[D_Amount] [int] NULL,
	[IsActive] [tinyint] NULL,
 CONSTRAINT [PK__VPrice__3214EC277606849C] PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[wifi_invoices]    Script Date: 4/9/2026 10:56:27 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[wifi_invoices](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[Invoice_No] [int] NOT NULL,
	[Item_Name] [nvarchar](255) NOT NULL,
	[Type] [nvarchar](255) NOT NULL,
	[QTY] [int] NOT NULL,
	[Created] [int] NOT NULL,
	[Remain]  AS ([QTY]-[Created]) PERSISTED,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
	[Cloud_ID] [int] NULL,
	[default] [int] NULL,
	[Username] [varchar](255) NULL,
 CONSTRAINT [PK__wifi_inv__3213E83FEA7EC493] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [activity_log_log_name_index]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [activity_log_log_name_index] ON [dbo].[activity_log]
(
	[log_name] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [causer]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [causer] ON [dbo].[activity_log]
(
	[causer_type] ASC,
	[causer_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [subject]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [subject] ON [dbo].[activity_log]
(
	[subject_type] ASC,
	[subject_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [idx]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [idx] ON [dbo].[collecting_history]
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [failed_jobs_uuid_unique]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [failed_jobs_uuid_unique] ON [dbo].[failed_jobs]
(
	[uuid] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [fblog_categories_name_unique]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [fblog_categories_name_unique] ON [dbo].[fblog_categories]
(
	[name] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [fblog_categories_slug_unique]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [fblog_categories_slug_unique] ON [dbo].[fblog_categories]
(
	[slug] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [fblog_news_letters_email_unique]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [fblog_news_letters_email_unique] ON [dbo].[fblog_news_letters]
(
	[email] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [fblog_tags_name_unique]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [fblog_tags_name_unique] ON [dbo].[fblog_tags]
(
	[name] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [fblog_tags_slug_unique]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [fblog_tags_slug_unique] ON [dbo].[fblog_tags]
(
	[slug] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [filachat_agents_agentable_id_agentable_type_unique]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [filachat_agents_agentable_id_agentable_type_unique] ON [dbo].[filachat_agents]
(
	[agentable_id] ASC,
	[agentable_type] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [filachat_messages_receiverable_id_receiverable_type_index]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [filachat_messages_receiverable_id_receiverable_type_index] ON [dbo].[filachat_messages]
(
	[receiverable_id] ASC,
	[receiverable_type] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [filachat_messages_senderable_id_senderable_type_index]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [filachat_messages_senderable_id_senderable_type_index] ON [dbo].[filachat_messages]
(
	[senderable_id] ASC,
	[senderable_type] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [health_check_result_history_items_batch_index]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [health_check_result_history_items_batch_index] ON [dbo].[health_check_result_history_items]
(
	[batch] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [health_check_result_history_items_created_at_index]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [health_check_result_history_items_created_at_index] ON [dbo].[health_check_result_history_items]
(
	[created_at] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [jobs_queue_index]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [jobs_queue_index] ON [dbo].[jobs]
(
	[queue] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [idx_lead_interaction_date]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [idx_lead_interaction_date] ON [dbo].[lead_interactions]
(
	[lead_id] ASC,
	[interaction_date] DESC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [IX_lead_phone]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [IX_lead_phone] ON [dbo].[leads]
(
	[phone] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [IX_lead_source]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [IX_lead_source] ON [dbo].[leads]
(
	[source] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [IX_lead_status]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [IX_lead_status] ON [dbo].[leads]
(
	[status] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [IX_Licenses]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [IX_Licenses] ON [dbo].[Licenses_Online]
(
	[LicensesID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [mail_logs_message_id_index]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [mail_logs_message_id_index] ON [dbo].[mail_logs]
(
	[message_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [mail_logs_status_index]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [mail_logs_status_index] ON [dbo].[mail_logs]
(
	[status] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [model_has_permissions_model_id_model_type_index]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [model_has_permissions_model_id_model_type_index] ON [dbo].[model_has_permissions]
(
	[model_id] ASC,
	[model_type] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [model_has_roles_model_id_model_type_index]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [model_has_roles_model_id_model_type_index] ON [dbo].[model_has_roles]
(
	[model_id] ASC,
	[model_type] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [note_metas_key_index]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [note_metas_key_index] ON [dbo].[note_metas]
(
	[key] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [notes_group_index]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [notes_group_index] ON [dbo].[notes]
(
	[group] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [notes_status_index]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [notes_status_index] ON [dbo].[notes]
(
	[status] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [notes_title_index]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [notes_title_index] ON [dbo].[notes]
(
	[title] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [notifications_notifiable_type_notifiable_id_index]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [notifications_notifiable_type_notifiable_id_index] ON [dbo].[notifications]
(
	[notifiable_type] ASC,
	[notifiable_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [notifications_templates_key_unique]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [notifications_templates_key_unique] ON [dbo].[notifications_templates]
(
	[key] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [password_resets_email_index]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [password_resets_email_index] ON [dbo].[password_resets]
(
	[email] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [permissions_name_guard_name_unique]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [permissions_name_guard_name_unique] ON [dbo].[permissions]
(
	[name] ASC,
	[guard_name] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [personal_access_tokens_token_unique]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [personal_access_tokens_token_unique] ON [dbo].[personal_access_tokens]
(
	[token] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [personal_access_tokens_tokenable_type_tokenable_id_index]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [personal_access_tokens_tokenable_type_tokenable_id_index] ON [dbo].[personal_access_tokens]
(
	[tokenable_type] ASC,
	[tokenable_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [IX_client_phone]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [IX_client_phone] ON [dbo].[price_quotes]
(
	[client_phone] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [IX_price_quotes_lead_id]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [IX_price_quotes_lead_id] ON [dbo].[price_quotes]
(
	[lead_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [IX_quote_number]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [IX_quote_number] ON [dbo].[price_quotes]
(
	[quote_number] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [IX_quote_status]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [IX_quote_status] ON [dbo].[price_quotes]
(
	[status] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [queue_monitors_failed_index]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [queue_monitors_failed_index] ON [dbo].[queue_monitors]
(
	[failed] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [queue_monitors_job_id_index]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [queue_monitors_job_id_index] ON [dbo].[queue_monitors]
(
	[job_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [queue_monitors_started_at_index]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [queue_monitors_started_at_index] ON [dbo].[queue_monitors]
(
	[started_at] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [IDX_Remote_Licenses_ID]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [IDX_Remote_Licenses_ID] ON [dbo].[Remote_Sub]
(
	[License_ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [roles_name_guard_name_unique]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [roles_name_guard_name_unique] ON [dbo].[roles]
(
	[name] ASC,
	[guard_name] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [sessions_last_activity_index]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [sessions_last_activity_index] ON [dbo].[sessions]
(
	[last_activity] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [sessions_user_id_index]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [sessions_user_id_index] ON [dbo].[sessions]
(
	[user_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [settings_group_name_unique]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [settings_group_name_unique] ON [dbo].[settings]
(
	[group] ASC,
	[name] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [shop_orders_number_unique]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [shop_orders_number_unique] ON [dbo].[shop_orders]
(
	[number] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [tickets_ticket_number_unique]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [tickets_ticket_number_unique] ON [dbo].[tickets]
(
	[ticket_number] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [types_metas_key_index]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [types_metas_key_index] ON [dbo].[types_metas]
(
	[key] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [users_email_unique]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE UNIQUE NONCLUSTERED INDEX [users_email_unique] ON [dbo].[users]
(
	[email] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
/****** Object:  Index [idx_cloudid]    Script Date: 4/9/2026 10:56:27 PM ******/
CREATE NONCLUSTERED INDEX [idx_cloudid] ON [dbo].[Vouchers]
(
	[cloud_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
GO
ALTER TABLE [dbo].[ApplicationVersions] ADD  DEFAULT (getdate()) FOR [ReleaseDate]
GO
ALTER TABLE [dbo].[ApplicationVersions] ADD  DEFAULT ((0)) FOR [Download_Times]
GO
ALTER TABLE [dbo].[ApplicationVersions] ADD  DEFAULT ((1)) FOR [IsActive]
GO
ALTER TABLE [dbo].[Clients] ADD  DEFAULT ((0)) FOR [ReferralBalance]
GO
ALTER TABLE [dbo].[Clients] ADD  DEFAULT ((0)) FOR [IsDist]
GO
ALTER TABLE [dbo].[collecting_history] ADD  DEFAULT (getdate()) FOR [Date]
GO
ALTER TABLE [dbo].[collecting_history] ADD  DEFAULT ((0)) FOR [Approved]
GO
ALTER TABLE [dbo].[daily_balance] ADD  DEFAULT (getdate()) FOR [Date]
GO
ALTER TABLE [dbo].[daily_stock] ADD  DEFAULT (getdate()) FOR [Date]
GO
ALTER TABLE [dbo].[events] ADD  CONSTRAINT [DF__events__created___6991A7CB]  DEFAULT (getdate()) FOR [created_at]
GO
ALTER TABLE [dbo].[events] ADD  CONSTRAINT [DF__events__updated___6A85CC04]  DEFAULT (getdate()) FOR [updated_at]
GO
ALTER TABLE [dbo].[events] ADD  CONSTRAINT [DF__events__status__0B7CAB7B]  DEFAULT ((0)) FOR [status]
GO
ALTER TABLE [dbo].[exports] ADD  DEFAULT ('0') FOR [processed_rows]
GO
ALTER TABLE [dbo].[exports] ADD  DEFAULT ('0') FOR [successful_rows]
GO
ALTER TABLE [dbo].[failed_jobs] ADD  DEFAULT (getdate()) FOR [failed_at]
GO
ALTER TABLE [dbo].[fblog_comments] ADD  DEFAULT ('0') FOR [approved]
GO
ALTER TABLE [dbo].[fblog_news_letters] ADD  DEFAULT ('1') FOR [subscribed]
GO
ALTER TABLE [dbo].[fblog_posts] ADD  DEFAULT ('pending') FOR [status]
GO
ALTER TABLE [dbo].[fblog_share_snippets] ADD  DEFAULT ('1') FOR [active]
GO
ALTER TABLE [dbo].[filachat_messages] ADD  DEFAULT ('0') FOR [is_starred]
GO
ALTER TABLE [dbo].[imports] ADD  DEFAULT ('0') FOR [processed_rows]
GO
ALTER TABLE [dbo].[imports] ADD  DEFAULT ('0') FOR [successful_rows]
GO
ALTER TABLE [dbo].[Invoices] ADD  DEFAULT (getdate()) FOR [InvoiceDate]
GO
ALTER TABLE [dbo].[item_master] ADD  DEFAULT ((1)) FOR [IsStock]
GO
ALTER TABLE [dbo].[Keys] ADD  DEFAULT ((0)) FOR [is_main]
GO
ALTER TABLE [dbo].[lead_interactions] ADD  DEFAULT ('note') FOR [type]
GO
ALTER TABLE [dbo].[lead_interactions] ADD  DEFAULT (getdate()) FOR [interaction_date]
GO
ALTER TABLE [dbo].[lead_interactions] ADD  DEFAULT (getdate()) FOR [created_at]
GO
ALTER TABLE [dbo].[lead_interactions] ADD  DEFAULT (getdate()) FOR [updated_at]
GO
ALTER TABLE [dbo].[leads] ADD  DEFAULT ('new') FOR [status]
GO
ALTER TABLE [dbo].[leads] ADD  DEFAULT (getdate()) FOR [created_at]
GO
ALTER TABLE [dbo].[leads] ADD  DEFAULT (getdate()) FOR [updated_at]
GO
ALTER TABLE [dbo].[Licenses] ADD  DEFAULT ((0)) FOR [IsActive]
GO
ALTER TABLE [dbo].[Logging] ADD  DEFAULT ((0)) FOR [Status]
GO
ALTER TABLE [dbo].[Mail] ADD  DEFAULT ((1)) FOR [IsActive]
GO
ALTER TABLE [dbo].[notes] ADD  DEFAULT ('pending') FOR [status]
GO
ALTER TABLE [dbo].[notes] ADD  DEFAULT ('#F4F39E') FOR [background]
GO
ALTER TABLE [dbo].[notes] ADD  DEFAULT ('#DEE184') FOR [border]
GO
ALTER TABLE [dbo].[notes] ADD  DEFAULT ('#47576B') FOR [color]
GO
ALTER TABLE [dbo].[notes] ADD  DEFAULT ('1em') FOR [font_size]
GO
ALTER TABLE [dbo].[notes] ADD  DEFAULT ('0') FOR [is_public]
GO
ALTER TABLE [dbo].[notes] ADD  DEFAULT ('0') FOR [is_pined]
GO
ALTER TABLE [dbo].[notes] ADD  DEFAULT ('0') FOR [order]
GO
ALTER TABLE [dbo].[notifications_logs] ADD  DEFAULT ('info') FOR [type]
GO
ALTER TABLE [dbo].[notifications_logs] ADD  DEFAULT ('fcm-api') FOR [provider]
GO
ALTER TABLE [dbo].[notifications_templates] ADD  DEFAULT ('heroicon-o-check-circle') FOR [icon]
GO
ALTER TABLE [dbo].[notifications_templates] ADD  DEFAULT ('success') FOR [type]
GO
ALTER TABLE [dbo].[notifications_templates] ADD  DEFAULT ('manual') FOR [action]
GO
ALTER TABLE [dbo].[payment_histories] ADD  DEFAULT (getdate()) FOR [transaction_date]
GO
ALTER TABLE [dbo].[payment_histories] ADD  DEFAULT ((0)) FOR [status]
GO
ALTER TABLE [dbo].[Payment_Method] ADD  DEFAULT ((1)) FOR [IsActive]
GO
ALTER TABLE [dbo].[price_quotes] ADD  DEFAULT ((0)) FOR [subtotal]
GO
ALTER TABLE [dbo].[price_quotes] ADD  DEFAULT ((0)) FOR [discount]
GO
ALTER TABLE [dbo].[price_quotes] ADD  DEFAULT ('fixed') FOR [discount_type]
GO
ALTER TABLE [dbo].[price_quotes] ADD  DEFAULT ((0)) FOR [total]
GO
ALTER TABLE [dbo].[price_quotes] ADD  DEFAULT ('draft') FOR [status]
GO
ALTER TABLE [dbo].[price_quotes] ADD  DEFAULT (getdate()) FOR [created_at]
GO
ALTER TABLE [dbo].[price_quotes] ADD  DEFAULT (getdate()) FOR [updated_at]
GO
ALTER TABLE [dbo].[purchasing] ADD  DEFAULT (getdate()) FOR [INV_Date]
GO
ALTER TABLE [dbo].[purchasing] ADD  DEFAULT (getdate()) FOR [INV_Time]
GO
ALTER TABLE [dbo].[queue_monitors] ADD  DEFAULT ('0') FOR [failed]
GO
ALTER TABLE [dbo].[queue_monitors] ADD  DEFAULT ('0') FOR [attempt]
GO
ALTER TABLE [dbo].[quote_line_items] ADD  DEFAULT ((1)) FOR [quantity]
GO
ALTER TABLE [dbo].[quote_line_items] ADD  DEFAULT (getdate()) FOR [created_at]
GO
ALTER TABLE [dbo].[quote_line_items] ADD  DEFAULT (getdate()) FOR [updated_at]
GO
ALTER TABLE [dbo].[quote_line_items] ADD  DEFAULT ('item') FOR [item_type]
GO
ALTER TABLE [dbo].[sales_target] ADD  DEFAULT ((0)) FOR [Achieved]
GO
ALTER TABLE [dbo].[settings] ADD  DEFAULT ('0') FOR [locked]
GO
ALTER TABLE [dbo].[shop_orders] ADD  DEFAULT ('new') FOR [status]
GO
ALTER TABLE [dbo].[stock_transaction] ADD  DEFAULT (getdate()) FOR [Transaction_Date]
GO
ALTER TABLE [dbo].[Technical_Support] ADD  DEFAULT ((1)) FOR [IsActive]
GO
ALTER TABLE [dbo].[ticket_events] ADD  DEFAULT ('COMMENT') FOR [type]
GO
ALTER TABLE [dbo].[ticket_events] ADD  DEFAULT ('0') FOR [private]
GO
ALTER TABLE [dbo].[tickets] ADD  DEFAULT ('1') FOR [unread]
GO
ALTER TABLE [dbo].[tickets] ADD  DEFAULT ('1') FOR [unread_user]
GO
ALTER TABLE [dbo].[tickets] ADD  DEFAULT ('0') FOR [closed]
GO
ALTER TABLE [dbo].[tickets] ADD  DEFAULT ('0') FOR [edited_title]
GO
ALTER TABLE [dbo].[tickets] ADD  DEFAULT ('0') FOR [reopened]
GO
ALTER TABLE [dbo].[Transfer_History] ADD  DEFAULT (getdate()) FOR [T_Date]
GO
ALTER TABLE [dbo].[types] ADD  DEFAULT ('posts') FOR [for]
GO
ALTER TABLE [dbo].[types] ADD  DEFAULT ('category') FOR [type]
GO
ALTER TABLE [dbo].[types] ADD  DEFAULT ('1') FOR [is_activated]
GO
ALTER TABLE [dbo].[user_has_notifications] ADD  DEFAULT ('pusher') FOR [provider]
GO
ALTER TABLE [dbo].[user_notifications] ADD  DEFAULT ('heroicon-o-check-circle') FOR [icon]
GO
ALTER TABLE [dbo].[user_notifications] ADD  DEFAULT ('success') FOR [type]
GO
ALTER TABLE [dbo].[user_notifications] ADD  DEFAULT ('public') FOR [privacy]
GO
ALTER TABLE [dbo].[user_read_notifications] ADD  DEFAULT ('0') FOR [read]
GO
ALTER TABLE [dbo].[user_read_notifications] ADD  DEFAULT ('0') FOR [open]
GO
ALTER TABLE [dbo].[wifi_invoices] ADD  DEFAULT ((0)) FOR [Created]
GO
ALTER TABLE [dbo].[event_histories]  WITH CHECK ADD  CONSTRAINT [IDX_Event_His_1] FOREIGN KEY([event_id])
REFERENCES [dbo].[events] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[event_histories] CHECK CONSTRAINT [IDX_Event_His_1]
GO
ALTER TABLE [dbo].[event_histories]  WITH CHECK ADD  CONSTRAINT [IDX_User_History_1] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[event_histories] CHECK CONSTRAINT [IDX_User_History_1]
GO
ALTER TABLE [dbo].[events]  WITH CHECK ADD  CONSTRAINT [FK_events_users] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[events] CHECK CONSTRAINT [FK_events_users]
GO
ALTER TABLE [dbo].[expense_histories]  WITH CHECK ADD  CONSTRAINT [expense_histories_created_by_foreign] FOREIGN KEY([created_by])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[expense_histories] CHECK CONSTRAINT [expense_histories_created_by_foreign]
GO
ALTER TABLE [dbo].[exports]  WITH CHECK ADD  CONSTRAINT [exports_user_id_foreign] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[exports] CHECK CONSTRAINT [exports_user_id_foreign]
GO
ALTER TABLE [dbo].[failed_import_rows]  WITH CHECK ADD  CONSTRAINT [failed_import_rows_import_id_foreign] FOREIGN KEY([import_id])
REFERENCES [dbo].[imports] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[failed_import_rows] CHECK CONSTRAINT [failed_import_rows_import_id_foreign]
GO
ALTER TABLE [dbo].[fblog_category_fblog_post]  WITH CHECK ADD  CONSTRAINT [fblog_category_fblog_post_category_id_foreign] FOREIGN KEY([category_id])
REFERENCES [dbo].[fblog_categories] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[fblog_category_fblog_post] CHECK CONSTRAINT [fblog_category_fblog_post_category_id_foreign]
GO
ALTER TABLE [dbo].[fblog_category_fblog_post]  WITH CHECK ADD  CONSTRAINT [fblog_category_fblog_post_post_id_foreign] FOREIGN KEY([post_id])
REFERENCES [dbo].[fblog_posts] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[fblog_category_fblog_post] CHECK CONSTRAINT [fblog_category_fblog_post_post_id_foreign]
GO
ALTER TABLE [dbo].[fblog_comments]  WITH CHECK ADD  CONSTRAINT [fblog_comments_post_id_foreign] FOREIGN KEY([post_id])
REFERENCES [dbo].[fblog_posts] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[fblog_comments] CHECK CONSTRAINT [fblog_comments_post_id_foreign]
GO
ALTER TABLE [dbo].[fblog_post_fblog_tag]  WITH CHECK ADD  CONSTRAINT [fblog_post_fblog_tag_post_id_foreign] FOREIGN KEY([post_id])
REFERENCES [dbo].[fblog_posts] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[fblog_post_fblog_tag] CHECK CONSTRAINT [fblog_post_fblog_tag_post_id_foreign]
GO
ALTER TABLE [dbo].[fblog_post_fblog_tag]  WITH CHECK ADD  CONSTRAINT [fblog_post_fblog_tag_tag_id_foreign] FOREIGN KEY([tag_id])
REFERENCES [dbo].[fblog_tags] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[fblog_post_fblog_tag] CHECK CONSTRAINT [fblog_post_fblog_tag_tag_id_foreign]
GO
ALTER TABLE [dbo].[fblog_posts]  WITH CHECK ADD  CONSTRAINT [fblog_posts_user_id_foreign] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[fblog_posts] CHECK CONSTRAINT [fblog_posts_user_id_foreign]
GO
ALTER TABLE [dbo].[fblog_seo_details]  WITH CHECK ADD  CONSTRAINT [fblog_seo_details_post_id_foreign] FOREIGN KEY([post_id])
REFERENCES [dbo].[fblog_posts] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[fblog_seo_details] CHECK CONSTRAINT [fblog_seo_details_post_id_foreign]
GO
ALTER TABLE [dbo].[filachat_messages]  WITH CHECK ADD  CONSTRAINT [filachat_messages_filachat_conversation_id_foreign] FOREIGN KEY([filachat_conversation_id])
REFERENCES [dbo].[filachat_conversations] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[filachat_messages] CHECK CONSTRAINT [filachat_messages_filachat_conversation_id_foreign]
GO
ALTER TABLE [dbo].[filachat_messages]  WITH CHECK ADD  CONSTRAINT [filachat_messages_reply_to_message_id_foreign] FOREIGN KEY([reply_to_message_id])
REFERENCES [dbo].[filachat_messages] ([id])
GO
ALTER TABLE [dbo].[filachat_messages] CHECK CONSTRAINT [filachat_messages_reply_to_message_id_foreign]
GO
ALTER TABLE [dbo].[imports]  WITH CHECK ADD  CONSTRAINT [imports_user_id_foreign] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[imports] CHECK CONSTRAINT [imports_user_id_foreign]
GO
ALTER TABLE [dbo].[Keys]  WITH CHECK ADD  CONSTRAINT [FK_Lic_Key] FOREIGN KEY([License_ID])
REFERENCES [dbo].[Licenses] ([ID])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[Keys] CHECK CONSTRAINT [FK_Lic_Key]
GO
ALTER TABLE [dbo].[lead_interactions]  WITH CHECK ADD  CONSTRAINT [fk_lead_interactions_lead] FOREIGN KEY([lead_id])
REFERENCES [dbo].[leads] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[lead_interactions] CHECK CONSTRAINT [fk_lead_interactions_lead]
GO
ALTER TABLE [dbo].[lead_interactions]  WITH CHECK ADD  CONSTRAINT [fk_lead_interactions_user] FOREIGN KEY([user_id])
REFERENCES [dbo].[users] ([id])
ON DELETE SET NULL
GO
ALTER TABLE [dbo].[lead_interactions] CHECK CONSTRAINT [fk_lead_interactions_user]
GO
ALTER TABLE [dbo].[leads]  WITH CHECK ADD FOREIGN KEY([assigned_to])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[leads]  WITH CHECK ADD FOREIGN KEY([created_by])
REFERENCES [dbo].[users] ([id])
GO
ALTER TABLE [dbo].[Licenses_Online]  WITH CHECK ADD  CONSTRAINT [FK_Licenses_Online] FOREIGN KEY([LicensesID])
REFERENCES [dbo].[Licenses] ([ID])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[Licenses_Online] CHECK CONSTRAINT [FK_Licenses_Online]
GO
ALTER TABLE [dbo].[Mail]  WITH CHECK ADD  CONSTRAINT [FK_Mail_Lic] FOREIGN KEY([License_ID])
REFERENCES [dbo].[Licenses] ([ID])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[Mail] CHECK CONSTRAINT [FK_Mail_Lic]
GO
ALTER TABLE [dbo].[model_has_permissions]  WITH CHECK ADD  CONSTRAINT [model_has_permissions_permission_id_foreign] FOREIGN KEY([permission_id])
REFERENCES [dbo].[permissions] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[model_has_permissions] CHECK CONSTRAINT [model_has_permissions_permission_id_foreign]
GO
ALTER TABLE [dbo].[model_has_roles]  WITH CHECK ADD  CONSTRAINT [model_has_roles_role_id_foreign] FOREIGN KEY([role_id])
REFERENCES [dbo].[roles] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[model_has_roles] CHECK CONSTRAINT [model_has_roles_role_id_foreign]
GO
ALTER TABLE [dbo].[monitored_scheduled_task_log_items]  WITH CHECK ADD  CONSTRAINT [fk_scheduled_task_id] FOREIGN KEY([monitored_scheduled_task_id])
REFERENCES [dbo].[monitored_scheduled_tasks] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[monitored_scheduled_task_log_items] CHECK CONSTRAINT [fk_scheduled_task_id]
GO
ALTER TABLE [dbo].[note_metas]  WITH CHECK ADD  CONSTRAINT [note_metas_note_id_foreign] FOREIGN KEY([note_id])
REFERENCES [dbo].[notes] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[note_metas] CHECK CONSTRAINT [note_metas_note_id_foreign]
GO
ALTER TABLE [dbo].[payment_histories]  WITH CHECK ADD  CONSTRAINT [payment_histories_approved_by_foreign] FOREIGN KEY([approved_by])
REFERENCES [dbo].[users] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[payment_histories] CHECK CONSTRAINT [payment_histories_approved_by_foreign]
GO
ALTER TABLE [dbo].[price_quotes]  WITH CHECK ADD  CONSTRAINT [FK_price_quotes_leads] FOREIGN KEY([lead_id])
REFERENCES [dbo].[leads] ([id])
ON DELETE SET NULL
GO
ALTER TABLE [dbo].[price_quotes] CHECK CONSTRAINT [FK_price_quotes_leads]
GO
ALTER TABLE [dbo].[quote_line_items]  WITH CHECK ADD FOREIGN KEY([item_id])
REFERENCES [dbo].[item_master] ([ID])
GO
ALTER TABLE [dbo].[quote_line_items]  WITH CHECK ADD FOREIGN KEY([quote_id])
REFERENCES [dbo].[price_quotes] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[Remote_Sub]  WITH CHECK ADD  CONSTRAINT [Remote_Licenses_1] FOREIGN KEY([License_ID])
REFERENCES [dbo].[Licenses] ([ID])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[Remote_Sub] CHECK CONSTRAINT [Remote_Licenses_1]
GO
ALTER TABLE [dbo].[role_has_permissions]  WITH CHECK ADD  CONSTRAINT [role_has_permissions_permission_id_foreign] FOREIGN KEY([permission_id])
REFERENCES [dbo].[permissions] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[role_has_permissions] CHECK CONSTRAINT [role_has_permissions_permission_id_foreign]
GO
ALTER TABLE [dbo].[role_has_permissions]  WITH CHECK ADD  CONSTRAINT [role_has_permissions_role_id_foreign] FOREIGN KEY([role_id])
REFERENCES [dbo].[roles] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[role_has_permissions] CHECK CONSTRAINT [role_has_permissions_role_id_foreign]
GO
ALTER TABLE [dbo].[Technical_Support]  WITH CHECK ADD  CONSTRAINT [FK_TS_Lic] FOREIGN KEY([LicenseID])
REFERENCES [dbo].[Licenses] ([ID])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[Technical_Support] CHECK CONSTRAINT [FK_TS_Lic]
GO
ALTER TABLE [dbo].[typables]  WITH CHECK ADD  CONSTRAINT [typables_type_id_foreign] FOREIGN KEY([type_id])
REFERENCES [dbo].[types] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[typables] CHECK CONSTRAINT [typables_type_id_foreign]
GO
ALTER TABLE [dbo].[types]  WITH CHECK ADD  CONSTRAINT [types_parent_id_foreign] FOREIGN KEY([parent_id])
REFERENCES [dbo].[types] ([id])
GO
ALTER TABLE [dbo].[types] CHECK CONSTRAINT [types_parent_id_foreign]
GO
ALTER TABLE [dbo].[types_metas]  WITH CHECK ADD  CONSTRAINT [types_metas_type_id_foreign] FOREIGN KEY([type_id])
REFERENCES [dbo].[types] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[types_metas] CHECK CONSTRAINT [types_metas_type_id_foreign]
GO
ALTER TABLE [dbo].[user_notifications]  WITH CHECK ADD  CONSTRAINT [user_notifications_created_by_foreign] FOREIGN KEY([created_by])
REFERENCES [dbo].[users] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[user_notifications] CHECK CONSTRAINT [user_notifications_created_by_foreign]
GO
ALTER TABLE [dbo].[user_notifications]  WITH CHECK ADD  CONSTRAINT [user_notifications_template_id_foreign] FOREIGN KEY([template_id])
REFERENCES [dbo].[notifications_templates] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[user_notifications] CHECK CONSTRAINT [user_notifications_template_id_foreign]
GO
ALTER TABLE [dbo].[user_read_notifications]  WITH CHECK ADD  CONSTRAINT [user_read_notifications_notification_id_foreign] FOREIGN KEY([notification_id])
REFERENCES [dbo].[user_notifications] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[user_read_notifications] CHECK CONSTRAINT [user_read_notifications_notification_id_foreign]
GO
ALTER TABLE [dbo].[events]  WITH CHECK ADD  CONSTRAINT [CK__events__type__00750D23] CHECK  (([type]='visit' OR [type]='followup' OR [type]='maintenance' OR [type]='new'))
GO
ALTER TABLE [dbo].[events] CHECK CONSTRAINT [CK__events__type__00750D23]
GO
ALTER TABLE [dbo].[fblog_posts]  WITH CHECK ADD  CONSTRAINT [CK__fblog_pos__statu__18EBB532] CHECK  (([status]=N'pending' OR [status]=N'scheduled' OR [status]=N'published'))
GO
ALTER TABLE [dbo].[fblog_posts] CHECK CONSTRAINT [CK__fblog_pos__statu__18EBB532]
GO
ALTER TABLE [dbo].[lead_interactions]  WITH CHECK ADD  CONSTRAINT [chk_interaction_type] CHECK  (([type]='other' OR [type]='note' OR [type]='quote_rejected' OR [type]='quote_accepted' OR [type]='quote_sent' OR [type]='visit' OR [type]='whatsapp' OR [type]='meeting' OR [type]='email' OR [type]='sms' OR [type]='call'))
GO
ALTER TABLE [dbo].[lead_interactions] CHECK CONSTRAINT [chk_interaction_type]
GO
ALTER TABLE [dbo].[shop_orders]  WITH CHECK ADD  CONSTRAINT [CK__shop_orde__statu__4BAC3F29] CHECK  (([status]=N'cancelled' OR [status]=N'delivered' OR [status]=N'shipped' OR [status]=N'processing' OR [status]=N'new'))
GO
ALTER TABLE [dbo].[shop_orders] CHECK CONSTRAINT [CK__shop_orde__statu__4BAC3F29]
GO
ALTER TABLE [dbo].[ticket_events]  WITH CHECK ADD  CONSTRAINT [CK__ticket_eve__type__65370702] CHECK  (([type]=N'DEPARTMENT_CHANGED' OR [type]=N'RE_OPEN' OR [type]=N'CLOSE' OR [type]=N'UN_ASSIGN' OR [type]=N'ASSIGN' OR [type]=N'COMMENT'))
GO
ALTER TABLE [dbo].[ticket_events] CHECK CONSTRAINT [CK__ticket_eve__type__65370702]
GO
/****** Object:  StoredProcedure [dbo].[Check_License_Request]    Script Date: 4/9/2026 10:56:27 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE PROCEDURE [dbo].[Check_License_Request]
  @I_Computer_ID AS VARCHAR(255) ,
  @O_Result AS INT OUTPUT 
AS
BEGIN
	-- routine body goes here, e.g.
	-- SELECT 'Navicat for SQL Server'
	
	SELECT @O_Result = COUNT(*)
	FROM dbo.Keys k
	WHERE k.Computer_ID = @I_Computer_ID;
	
END
GO
/****** Object:  StoredProcedure [dbo].[Check_Licenses_Valid]    Script Date: 4/9/2026 10:56:27 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE PROCEDURE [dbo].[Check_Licenses_Valid]
  @I_Computer_ID AS VARCHAR(255) ,
  @O_Result AS INT OUTPUT 
AS
BEGIN

SELECT @O_Result = COUNT(*)
	FROM dbo.Keys k
	INNER JOIN dbo.Licenses l ON k.License_ID = l.ID 
	WHERE k.Computer_ID = @I_Computer_ID AND l.IsActive = 1;
	
END
GO
/****** Object:  StoredProcedure [dbo].[Check_Mail]    Script Date: 4/9/2026 10:56:27 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE PROCEDURE [dbo].[Check_Mail]
  @I_Computer_ID AS VARCHAR(255) ,
  @O_Result AS int OUTPUT,
	@O_Server AS VARCHAR(255) OUTPUT,
	@O_Email AS VARCHAR(255) OUTPUT,
	@O_Password AS VARCHAR(255) OUTPUT,
	@O_Helo_Name As VARCHAR(255) OUTPUT,
	@O_Status As VARCHAR(255) OUTPUT
	
AS
BEGIN


SELECT TOP 1 @O_Result = COUNT(*)
	FROM dbo.Keys k
	INNER JOIN dbo.Mail l ON k.License_ID = l.License_ID
	WHERE k.Computer_ID = @I_Computer_ID AND l.IsActive = 1;
	
	if(@O_Result = 1) 
	BEGIN
	SELECT @O_Server = MailConfig.[Value] FROM MailConfig WHERE MailConfig.Description = 'Server';
	SELECT @O_Email = MailConfig.[Value] FROM MailConfig WHERE MailConfig.Description = 'Email';
	SELECT @O_Password = MailConfig.[Value] FROM MailConfig WHERE MailConfig.Description = 'Password';
	SELECT @O_Helo_Name = MailConfig.[Value] FROM MailConfig WHERE MailConfig.Description = 'HeloName';
	SELECT @O_Status = MailConfig.[Value] FROM MailConfig WHERE MailConfig.Description = 'Status';
	END
END
GO
/****** Object:  StoredProcedure [dbo].[Collecting_Money]    Script Date: 4/9/2026 10:56:27 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE PROCEDURE [dbo].[Collecting_Money]
  @I_From AS varchar(255) ,
  @I_To AS varchar(255) ,
  @I_AMT AS money ,
	@I_Username VARCHAR(255),
	@I_Reason VARCHAR(255)
AS
BEGIN
	
	-- Insert History 
	INSERT INTO collecting_history(M_From,M_To,Money,Username,Date,Reason) VALUES (@I_From,@I_To,@I_AMT,@I_Username,GETDATE(),@I_Reason);
	
	-- Decrease From 
	UPDATE Staff SET Staff.Balance = Balance + @I_AMT WHERE Staff.name = @I_From;
	
	-- Increase To 
	UPDATE Staff SET Staff.Balance = Balance - @I_AMT WHERE Staff.name = @I_To;
	
END
GO
/****** Object:  StoredProcedure [dbo].[Generate_License]    Script Date: 4/9/2026 10:56:27 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE PROCEDURE [dbo].[Generate_License]
  @I_Username AS varchar(max) ,
  @I_License_Type AS varchar(max) ,
  @I_Edition AS INT,
  @I_License_ID AS INT 
	AS
BEGIN

  -- Update Licenses Price

	DECLARE @Voucher_ID INT,@O_Client_ID INT,@O_Invoice_No INT,@O_Product_ID INT,@O_Product_Price INT,@O_Item_Name VARCHAR(255),@Current_Type VARCHAR(25),@O_Sub_Price INT;

	SELECT @O_Client_ID = Licenses.ClientID ,@Current_Type = Licenses.LicenseType, @O_Product_ID = ProductID FROM Licenses WHERE ID = @I_License_ID;
	SELECT @O_Item_Name = Products.Product_Name FROM Products WHERE ID = @O_Product_ID;
  	SELECT @O_Product_Price = price From product_editions WHERE id = @I_Edition;
	SELECT @O_Invoice_No = ISNULL(MAX(Invoice_No), 0) + 1 FROM Invoices;
	DELETE FROM Technical_Support WHERE Technical_Support.LicenseID = @I_License_ID;
	DELETE FROM Mail WHERE Mail.License_ID = @I_License_ID;
	-- Case 1 : Licenses Type Full
	IF(@I_License_Type = 'FULL')
	BEGIN
    
		UPDATE Licenses SET LicenseType = @I_License_Type , Period = 0 , StartDate = CONVERT(date,GETDATE()), Approved_By = @I_Username , IsActive = 1 , Edition_ID = @I_Edition WHERE ID = @I_License_ID ;
		-- Insert Invoice
		INSERT INTO Invoices (LicenseID,ClientID,Invoice_No,Item_Name,QTY,Price,Amount,Username,Type,Description )
		VALUES
		(@I_License_ID,@O_Client_ID,@O_Invoice_No,@O_Item_Name,1,@O_Product_Price,@O_Product_Price,@I_Username,'Software','Licenses');
		UPDATE users SET balance = balance - @O_Product_Price WHERE users.name = @I_Username;
		-- UPDATE Keys SET License_Key = @I_License_Key WHERE License_ID = @I_License_ID;

		INSERT INTO Technical_Support(LicenseID,Start_Date,End_Date) VALUES ( @I_License_ID,GETDATE(),DATEADD(YEAR, 1, GETDATE()));
		INSERT INTO Mail(Mail.License_ID,Mail.Start_Date,End_Date) VALUES ( @I_License_ID,GETDATE(),DATEADD(YEAR, 1, GETDATE()));
   		SELECT @O_Sub_Price = ProductsFeatures.FeatureAmount FROM dbo.ProductsFeatures LEFT JOIN dbo.Licenses ON ProductsFeatures.ProductID = Licenses.Edition_ID WHERE Licenses.ID = @I_License_ID AND ProductsFeatures.FeatureName = 'Technical Support';
		
		INSERT INTO Invoices (LicenseID,ClientID,Invoice_No,Item_Name,QTY,Price,Amount,InvoiceDate,Username,Type,Description) VALUES (@I_License_ID,@O_Client_ID,@O_Invoice_No,'Technical Support',1,@O_Sub_Price,@O_Sub_Price,GETDATE(),@I_Username,'Software','New Subscription') ;
		UPDATE users SET balance = balance - @O_Sub_Price WHERE name = @I_Username;
--		SELECT @O_Invoice_No = ISNULL(MAX(Invoice_No), 0) + 1 FROM Invoices;
   	SELECT @O_Sub_Price = ProductsFeatures.FeatureAmount FROM dbo.ProductsFeatures LEFT JOIN dbo.Licenses ON ProductsFeatures.ProductID =Licenses.Edition_ID WHERE Licenses.ID = @I_License_ID AND ProductsFeatures.FeatureName = 'Mail';
		INSERT INTO Invoices (LicenseID,ClientID,Invoice_No,Item_Name,QTY,Price,Amount,InvoiceDate,Username,Type,Description) VALUES (@I_License_ID,@O_Client_ID,@O_Invoice_No,'Mail',1,@O_Sub_Price,@O_Sub_Price,GETDATE(),@I_Username,'Software','New Subscription') ;
		UPDATE users SET balance = balance - @O_Sub_Price WHERE name = @I_Username;
  -- update sales target
  UPDATE sales_target SET sales_target.Achieved = Achieved + 1 WHERE Type = 'Software' AND Type_ID = @O_Product_ID AND Username = 'Admin' AND Target_Month = FORMAT(getdate(),'yyyy-MM');
	END
	Else IF(@I_License_Type = 'TRIAL')
	BEGIN
	IF(@Current_Type IS NULL)
		BEGIN
			UPDATE Licenses SET LicenseType = @I_License_Type , Period = 7 , StartDate = CONVERT(date,GETDATE()),EndDate = DATEADD(DAY, 7, CONVERT(date,GETDATE())), Approved_By = @I_Username , IsActive = 1, Edition_ID = 1 WHERE ID = @I_License_ID ;
			INSERT INTO Technical_Support(LicenseID,Start_Date,End_Date) VALUES ( @I_License_ID,GETDATE(),DATEADD(DAY, 7, GETDATE()));
			INSERT INTO Mail(Mail.License_ID,Mail.Start_Date,End_Date) VALUES ( @I_License_ID,GETDATE(),DATEADD(DAY, 7, GETDATE()));
		END
	END
	ELSE IF(@I_License_Type = 'MONTHLY')
	BEGIN
		IF(@Current_Type = 'MONTHLY')
		BEGIN
			UPDATE Licenses SET LicenseType = @I_License_Type , Period = 30 , EndDate = DATEADD(MONTH, 1, CONVERT(date,GETDATE())), Approved_By = @I_Username , IsActive = 1 , Edition_ID = 1  WHERE ID = @I_License_ID;
			SELECT @O_Product_Price = product_rent.monthly FROM product_rent WHERE product_id = @O_Product_ID;
			INSERT INTO Invoices (LicenseID,ClientID,Invoice_No,Item_Name,QTY,Price,Amount,Username,Type,Description )
			VALUES
			(@I_License_ID,@O_Client_ID,@O_Invoice_No,@O_Item_Name,1,@O_Product_Price,@O_Product_Price,@I_Username,'Software','Licenses Monthly');
			UPDATE users SET balance = balance - @O_Product_Price WHERE users.name = @I_Username;
		END
		ELSE
		BEGIN
			UPDATE Licenses SET LicenseType = @I_License_Type , Period = 30 , StartDate = CONVERT(date,GETDATE()),EndDate = DATEADD(MONTH, 1, CONVERT(date,GETDATE())), Approved_By = @I_Username , IsActive = 1, Edition_ID = 1 WHERE ID = @I_License_ID ;
			INSERT INTO Technical_Support(LicenseID,Start_Date,End_Date) VALUES ( @I_License_ID,GETDATE(),DATEADD(YEAR, 1, GETDATE()));
			-- SELECT @O_Invoice_No = ISNULL(MAX(Invoice_No), 0) + 1 FROM Invoices;
	   		SELECT @O_Sub_Price = ProductsFeatures.FeatureAmount FROM dbo.ProductsFeatures LEFT JOIN dbo.Licenses ON ProductsFeatures.ProductID = Licenses.Edition_ID WHERE Licenses.ID = @I_License_ID AND ProductsFeatures.FeatureName = 'Technical Support';
			INSERT INTO Invoices (LicenseID,ClientID,Invoice_No,Item_Name,QTY,Price,Amount,InvoiceDate,Username,Type,Description) VALUES (@I_License_ID,@O_Client_ID,@O_Invoice_No,'Technical Support',1,@O_Sub_Price,@O_Sub_Price,GETDATE(),@I_Username,'Software','New Subscription') ;
			UPDATE users SET balance = balance - @O_Sub_Price WHERE name = @I_Username;
		END
	END
	ELSE IF(@I_License_Type = 'ANNUAL')
	BEGIN
		IF(@Current_Type = 'ANNUAL')
		BEGIN
			UPDATE Licenses SET LicenseType = @I_License_Type , Period = 365 , EndDate = DATEADD(YEAR, 1, CONVERT(date,GETDATE())), Approved_By = @I_Username , IsActive = 1 , Edition_ID = 1 WHERE ID = @I_License_ID;
			SELECT @O_Product_Price = product_rent.yearly FROM product_rent WHERE product_id = @O_Product_ID;
			INSERT INTO Invoices (LicenseID,ClientID,Invoice_No,Item_Name,QTY,Price,Amount,Username,Type,Description )
			VALUES
			(@I_License_ID,@O_Client_ID,@O_Invoice_No,@O_Item_Name,1,@O_Product_Price,@O_Product_Price,@I_Username,'Software','Licenses ANNUAL');
			UPDATE users SET balance = balance - @O_Product_Price WHERE users.name = @I_Username;
		END
	END

SELECT @Voucher_ID = MAX(Voucher_ID) FROM Invoices WHERE LicenseID = @I_License_ID;
IF(@Voucher_ID IS NOT NULL) BEGIN
	UPDATE Clients SET Clients.[ReferralBalance ] = [ReferralBalance ] + 200 WHERE ID = (SELECT voucher.ReferrerID FROM voucher WHERE ID = @Voucher_ID);
	END

END
GO
/****** Object:  StoredProcedure [dbo].[Insert_Invoice]    Script Date: 4/9/2026 10:56:27 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE PROCEDURE [dbo].[Insert_Invoice]
  @I_Licenses_ID AS INT ,
  @I_Client_ID AS INT ,
  @I_Item_Name AS VARCHAR(MAX) ,
  @I_QTY AS INT ,
  @I_Price AS DECIMAL(11,2) ,
  @I_Username AS VARCHAR(MAX) ,
  @O_Result AS VARCHAR(255) OUTPUT 
AS
BEGIN
	-- routine body goes here, e.g.
	-- SELECT 'Navicat for SQL Server'
	
	DECLARE @O_Invoice_No INT,@O_Cloud_ID INT, @O_NotDist INT, @O_Take_Before INT,@Item_Barcode VARCHAR(255);
	
	SELECT @O_Invoice_No = ISNULL(MAX(Invoice_No), 0) + 1 FROM Invoices;
	
	INSERT INTO Invoices (LicenseID, ClientID, Type, Description, Invoice_No, Item_Name, QTY, Price, Amount, InvoiceDate, Username)
    VALUES (@I_Licenses_ID, @I_Client_ID, 'Hardware', 'Hardware', @O_Invoice_No, @I_Item_Name, @I_QTY, @I_Price, @I_QTY * @I_Price, CONVERT(DATE, GETDATE()), @I_Username);

    -- Update User Balance
    UPDATE users 
    SET balance = balance - (@I_QTY * @I_Price)
    WHERE name = @I_Username;

    SET @O_Result = 'تم تسجيل الفاتورة .';
IF EXISTS(SELECT 1 FROM item_master WHERE Item_Name = @I_Item_Name AND IsStock = 1)
BEGIN
    -- Handling BOM Products
    IF EXISTS (SELECT 1 FROM item_master WHERE Item_Name = @I_Item_Name AND IsProduct = 1)
    BEGIN
        DECLARE @Product_Code VARCHAR(255);
        SELECT @Product_Code = Code FROM item_master WHERE Item_Name = @I_Item_Name;

        -- Use MERGE Instead of Cursor
        MERGE stock AS target
        USING (SELECT b.Item_ID, b.Quantity FROM BOM b WHERE b.Product_Code = @Product_Code AND b.IsActive = 1) AS source
        ON target.Inventory = @I_Username AND target.Barcode = source.Item_ID
        WHEN MATCHED THEN
            UPDATE SET target.Quantity = target.Quantity - (source.Quantity * @I_QTY)
        WHEN NOT MATCHED THEN
            INSERT (Inventory, Barcode, Quantity)
            VALUES (@I_Username, source.Item_ID, -(source.Quantity * @I_QTY));
        INSERT INTO stock_transaction (Barcode, Transaction_Type, Quantity, Inventory, Reference)
        SELECT b.Item_ID, 'Sales', b.Quantity * @I_QTY, @I_Username, @O_Invoice_No
        FROM BOM b
        WHERE b.Product_Code = @Product_Code AND b.IsActive = 1;
    END
    ELSE
    BEGIN
        
        SELECT @Item_Barcode = Code FROM item_master WHERE Item_Name = @I_Item_Name;

        -- Use MERGE for Direct Item Stock Update
        MERGE stock AS target
        USING (SELECT @Item_Barcode AS Barcode, @I_QTY AS Quantity) AS source
        ON target.Inventory = @I_Username AND target.Barcode = source.Barcode
        WHEN MATCHED THEN
            UPDATE SET target.Quantity = target.Quantity - source.Quantity
        WHEN NOT MATCHED THEN
            INSERT (Inventory, Barcode, Quantity)
            VALUES (@I_Username, @Item_Barcode, -@I_QTY);
        INSERT INTO stock_transaction (Barcode, Transaction_Type, Quantity, Inventory, Reference)
        SELECT @Item_Barcode, 'Sales', @I_QTY, @I_Username, @O_Invoice_No;

    END
END

    -- Free Voucher Logic
    IF @I_Item_Name = 'Access Point 7200'
    BEGIN 
        SELECT @O_NotDist = Clients.IsDist FROM Clients WHERE ID = @I_Client_ID;
        IF @O_NotDist = 0 
        BEGIN
        PRINT 'Not Dist';
            SET @O_Take_Before = 0;
            SELECT @O_Take_Before = COUNT(Invoices.Invoice_No) FROM Invoices WHERE ClientID = @I_Client_ID AND Description = 'Wi-Fi Voucher';
            IF @O_Take_Before = 0
            BEGIN
              PRINT 'Not take before';
              SELECT @O_Cloud_ID =
                  CASE 
                      WHEN CHARINDEX(',', Cloud_ID) > 0 THEN NULL  -- If there is a comma, set NULL
                      ELSE Cloud_ID 
                  END
              FROM Clients 
              WHERE ID = @I_Client_ID;                
                IF @O_Cloud_ID IS NOT NULL
                  BEGIN
                  PRINT 'Cloud ID ';
      
                  SELECT @O_Invoice_No = ISNULL(MAX(Invoice_No), 0) + 1 FROM Invoices;
                  
                  INSERT INTO Invoices (ClientID, Type, Description, Invoice_No, Item_Name, QTY, Price, Amount, InvoiceDate, Username) 
                  VALUES (@I_Client_ID, 'Wi-Fi', 'Wi-Fi Voucher', @O_Invoice_No, 'Wi-Fi Voucher 1000 EA', 1, 0, 0, CONVERT(DATE, GETDATE()), @I_Username);

                  INSERT INTO wifi_invoices (Invoice_No, Item_Name, Type, QTY, Cloud_ID, Username,created_at,updated_at) 
                  VALUES (@O_Invoice_No, 'Wi-Fi Voucher 1000 EA', 'Limted', 1000, @O_Cloud_ID, @I_Username,GETDATE(),GETDATE());
                END
            END
        END
    END
    
    	-- Update Sales Target
        UPDATE sales_target SET sales_target.Achieved = Achieved + 1 WHERE Type = 'Hardware' AND Type_ID = @Item_Barcode AND Username = 'Admin' AND Target_Month = FORMAT(getdate(),'yyyy-MM');

END
GO
/****** Object:  StoredProcedure [dbo].[Insert_Subscription]    Script Date: 4/9/2026 10:56:27 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE PROCEDURE [dbo].[Insert_Subscription]
  @I_Licenses_ID AS int ,
  @I_Type AS varchar(255) ,
  @I_Username AS varchar(255) 
AS
BEGIN
	-- routine body goes here, e.g.
	-- SELECT 'Navicat for SQL Server'
	DECLARE @O_Client_ID INT,@O_Invoice_No INT, @O_Exist INT,@O_Sub_Price INT;
	
	SELECT @O_Client_ID = Licenses.ClientID FROM Licenses WHERE ID = @I_Licenses_ID;
	SELECT @O_Invoice_No = ISNULL(MAX(Invoice_No), 0) + 1 FROM Invoices;
	
	if(@I_Type = 'Technical Support')
	BEGIN
  
    SELECT @O_Sub_Price = ProductsFeatures.FeatureAmount FROM dbo.ProductsFeatures LEFT JOIN dbo.Licenses ON ProductsFeatures.ProductID =Licenses.Edition_ID WHERE Licenses.ID = @I_Licenses_ID AND ProductsFeatures.FeatureName = 'Technical Support';
		SELECT @O_Exist = IsActive FROM Technical_Support WHERE LicenseID = @I_Licenses_ID;
    IF(@O_Exist = 1)
      BEGIN
        UPDATE Technical_Support SET End_Date = DATEADD(YEAR, 1, End_Date) WHERE LicenseID = @I_Licenses_ID;
  -- 		DELETE FROM Technical_Support WHERE Technical_Support.LicenseID = @I_Licenses_ID;
      END
    ELSE
      BEGIN
        INSERT INTO Technical_Support(LicenseID,Start_Date,End_Date) VALUES ( @I_Licenses_ID,GETDATE(),DATEADD(YEAR, 1, GETDATE()));
      END
		INSERT INTO Invoices (LicenseID,ClientID,Type,Description,Invoice_No,Item_Name,QTY,Price,Amount,InvoiceDate,Username) VALUES (@I_Licenses_ID,@O_Client_ID,'Software','Subscription',@O_Invoice_No,@I_Type,1,@O_Sub_Price,@O_Sub_Price,GETDATE(),@I_Username) ;
		UPDATE users SET balance = balance - @O_Sub_Price WHERE name = @I_Username;
	END
	if(@I_Type = 'Mail')
	BEGIN
  SELECT @O_Sub_Price = ProductsFeatures.FeatureAmount FROM dbo.ProductsFeatures LEFT JOIN dbo.Licenses ON ProductsFeatures.ProductID =Licenses.Edition_ID WHERE Licenses.ID = @I_Licenses_ID AND ProductsFeatures.FeatureName = 'Mail';
 		SELECT @O_Exist = IsActive FROM Mail WHERE License_ID = @I_Licenses_ID;
    IF(@O_Exist = 1)
      BEGIN
        UPDATE Mail SET End_Date = DATEADD(YEAR, 1, End_Date) WHERE License_ID = @I_Licenses_ID;
  -- 		DELETE FROM Technical_Support WHERE Technical_Support.LicenseID = @I_Licenses_ID;
      END
    ELSE
      BEGIN
        INSERT INTO Mail(Mail.License_ID,Mail.Start_Date,End_Date) VALUES ( @I_Licenses_ID,GETDATE(),DATEADD(YEAR, 1, GETDATE()));
      END
    INSERT INTO Invoices (LicenseID,ClientID,Type,Description,Invoice_No,Item_Name,QTY,Price,Amount,InvoiceDate,Username) VALUES (@I_Licenses_ID,@O_Client_ID,'Software','Subscription',@O_Invoice_No,@I_Type,1,@O_Sub_Price,@O_Sub_Price,GETDATE(),@I_Username) ;
		UPDATE users SET balance = balance - @O_Sub_Price WHERE name = @I_Username;
	END
  
  if(@I_Type = 'Remote')
  BEGIN
      SELECT @O_Sub_Price = ProductsFeatures.FeatureAmount FROM dbo.ProductsFeatures LEFT JOIN dbo.Licenses ON ProductsFeatures.ProductID =Licenses.Edition_ID WHERE Licenses.ID = @I_Licenses_ID AND ProductsFeatures.FeatureName = 'Remote';
      
--       SELECT @O_Exist = IsActive FROM Mail WHERE License_ID = @I_Licenses_ID;
--     IF(@O_Exist = 1)
--       BEGIN
-- --         UPDATE Mail SET End_Date = DATEADD(YEAR, 1, End_Date) WHERE License_ID = @I_Licenses_ID;
--   
--       END
--     ELSE
--       BEGIN
        INSERT INTO Remote_Sub(Remote_Sub.License_ID,Remote_Sub.Start_Date,End_Date,IsActive) VALUES ( @I_Licenses_ID,GETDATE(),'2026-04-15',1);
--       END
--     INSERT INTO Invoices (LicenseID,ClientID,Type,Description,Invoice_No,Item_Name,QTY,Price,Amount,InvoiceDate,Username) VALUES (@I_Licenses_ID,@O_Client_ID,'Software','Subscription',@O_Invoice_No,@I_Type,1,@O_Sub_Price,@O_Sub_Price,GETDATE(),@I_Username) ;
-- 		UPDATE users SET balance = balance - @O_Sub_Price WHERE name = @I_Username;

  END
END
GO
/****** Object:  StoredProcedure [dbo].[Insert_Subscription_Client]    Script Date: 4/9/2026 10:56:27 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE PROCEDURE [dbo].[Insert_Subscription_Client]
  @I_Licenses_ID AS int ,
  @I_Type AS varchar(255) ,
  @I_Client_ID AS int
AS
BEGIN
	-- routine body goes here, e.g.
	-- SELECT 'Navicat for SQL Server'
	DECLARE @O_Name VARCHAR(255),@O_Invoice_No INT, @O_Exist INT,@O_Sub_Price INT;
	
-- 	SELECT @O_Client_ID = Licenses.ClientID FROM Licenses WHERE ID = @I_Licenses_ID;
  SELECT @O_Name = Clients.name FROM Clients WHERE Clients.id = @I_Client_ID;
	SELECT @O_Invoice_No = ISNULL(MAX(Invoice_No), 0) + 1 FROM Invoices;
	
	if(@I_Type = 'Technical Support')
	BEGIN
    SELECT @O_Exist = IsActive FROM Technical_Support WHERE LicenseID = @I_Licenses_ID;
    SELECT @O_Sub_Price = ProductsFeatures.FeatureAmount FROM dbo.ProductsFeatures LEFT JOIN dbo.Licenses ON ProductsFeatures.ProductID =Licenses.Edition_ID WHERE Licenses.ID = @I_Licenses_ID AND ProductsFeatures.FeatureName = 'Technical Support';
    IF(@O_Exist = 1)
      BEGIN
        UPDATE Technical_Support SET End_Date = DATEADD(YEAR, 1, End_Date) WHERE LicenseID = @I_Licenses_ID;
  -- 		DELETE FROM Technical_Support WHERE Technical_Support.LicenseID = @I_Licenses_ID;
      END
    ELSE
      BEGIN
        INSERT INTO Technical_Support(LicenseID,Start_Date,End_Date) VALUES ( @I_Licenses_ID,GETDATE(),DATEADD(YEAR, 1, GETDATE()));
      END
    
		INSERT INTO Invoices (LicenseID,ClientID,Type,Description,Invoice_No,Item_Name,QTY,Price,Amount,InvoiceDate,Username) VALUES (@I_Licenses_ID,@I_Client_ID,'Software','Subscription',@O_Invoice_No,@I_Type,1,@O_Sub_Price,@O_Sub_Price,GETDATE(),@O_Name) ;
    UPDATE Clients SET ReferralBalance = ReferralBalance - @O_Sub_Price WHERE id = @I_Client_ID;

	END
	if(@I_Type = 'Mail')
	BEGIN
  SELECT @O_Sub_Price = ProductsFeatures.FeatureAmount FROM dbo.ProductsFeatures LEFT JOIN dbo.Licenses ON ProductsFeatures.ProductID =Licenses.Edition_ID WHERE Licenses.ID = @I_Licenses_ID AND ProductsFeatures.FeatureName = 'Mail';
    SELECT @O_Exist = IsActive FROM Mail WHERE License_ID = @I_Licenses_ID;
    IF(@O_Exist = 1)
      BEGIN
        UPDATE Mail SET End_Date = DATEADD(YEAR, 1, End_Date) WHERE License_ID = @I_Licenses_ID;
  -- 		DELETE FROM Technical_Support WHERE Technical_Support.LicenseID = @I_Licenses_ID;
      END
    ELSE
      BEGIN
        INSERT INTO Mail(Mail.License_ID,Mail.Start_Date,End_Date) VALUES ( @I_Licenses_ID,GETDATE(),DATEADD(YEAR, 1, GETDATE()));
      END
    
    INSERT INTO Invoices (LicenseID,ClientID,Type,Description,Invoice_No,Item_Name,QTY,Price,Amount,InvoiceDate,Username) VALUES (@I_Licenses_ID,@I_Client_ID,'Software','Subscription',@O_Invoice_No,@I_Type,1,@O_Sub_Price,@O_Sub_Price,GETDATE(),@O_Name) ;
		UPDATE Clients SET ReferralBalance = ReferralBalance - @O_Sub_Price WHERE id = @I_Client_ID;
	END
END
GO
/****** Object:  StoredProcedure [dbo].[Renew_Monthly]    Script Date: 4/9/2026 10:56:27 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE PROCEDURE [dbo].[Renew_Monthly]
  @I_Lic_ID AS INT ,
	@I_Username AS VARCHAR(25) ,
	@O_ComputerID AS VARCHAR(255) OUTPUT,
	@O_KeyID AS VARCHAR(255) OUTPUT
AS
BEGIN
	-- routine body goes here, e.g.
	-- SELECT 'Navicat for SQL Server'
	DECLARE @O_Client_ID INT, @O_Invoice_No INT;
		
	SELECT @O_KeyID = Keys.ID, @O_ComputerID = Keys.Computer_ID FROM Keys WHERE License_ID = @I_Lic_ID;
	SELECT @O_Invoice_No = ISNULL(MAX(Invoice_No), 0) + 1 FROM Invoices;
	INSERT INTO Invoices (LicenseID,ClientID,Invoice_No,Item_Name,QTY,Price,Amount,Username,Type,Description ) 
	VALUES
	(@I_Lic_ID,@O_Client_ID,@O_Invoice_No,'PlayStation Rent',1,100,100,@I_Username,'Software','Renew Monthly');
	UPDATE users SET balance = balance - 100 WHERE name = @I_Username;
	
	UPDATE Licenses SET Licenses.EndDate = DATEADD(MONTH, 1, Licenses.EndDate) WHERE ID = @I_Lic_ID;		
END
GO
/****** Object:  StoredProcedure [dbo].[report_event]    Script Date: 4/9/2026 10:56:27 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE PROCEDURE [dbo].[report_event]
@doc ntext
AS

SET NOCOUNT ON

DECLARE @idoc int
EXEC sp_xml_preparedocument @idoc OUTPUT, @doc

/*

Combine multiple System-Health-Result string into one string with delimiter as ":"

*/

DECLARE @SystemHealthResult NVARCHAR(4000)
SELECT @SystemHealthResult = COALESCE(@SystemHealthResult+':','')+ISNULL(SHR.System_Health_Result,'')
FROM (SELECT *
FROM OPENXML(@idoc, '/Event/System-Health-Result')
WITH (System_Health_Result NVARCHAR(4000) 'text()')) AS SHR

/*

Combine multiple System-Health-ResultEx string into one string

*/

DECLARE @SystemHealthResultEx NVARCHAR(MAX)
IF @SystemHealthResult IS NOT NULL SELECT @SystemHealthResultEx = COALESCE(@SystemHealthResultEx,'')+ISNULL(CAST(SHR.System_Health_ResultEx AS NVARCHAR(MAX)),'')
FROM (SELECT *
FROM OPENXML(@idoc, '/Event/System-Health-ResultEx')
WITH (System_Health_ResultEx xml '.')) AS SHR


/*

All RADIUS attributes written to the ODBC format logfile are declared here. Refer to IAS ODBC Formatted Log Files in Online Help for information on interpreting these values.

*/

INSERT accounting_data
SELECT
	Timestamp,
	Computer_Name,
	Packet_Type,
	[User_Name],
	Client_IP_Address,
	Fully_Qualified_Machine_Name,
	NP_Policy_Name,
	MS_Quarantine_State,
	MS_Extended_Quarantine_State,
	@SystemHealthResult,
	@SystemHealthResultEx,
	MS_Network_Access_Server_Type,
	Called_Station_Id,
	MS_Quarantine_Grace_Time,
	MS_Quarantine_User_Class,
	Client_IPv6_Address,
	Not_Quarantine_Capable,
	AFW_Zone,
	AFW_Protection_Level,
	Quarantine_Update_Non_Compliant,
	MS_Machine_Name,
	OS_Version,
	MS_Quarantine_Session_Id
FROM OPENXML(@idoc, '/Event')
WITH (
	Timestamp datetime './Timestamp',
	Computer_Name nvarchar(255) './Computer-Name',
	Packet_Type int './Packet-Type',
	[User_Name] nvarchar(255) './User-Name',
	Client_IP_Address nvarchar(15) './Client-IP-Address',
	Fully_Qualified_Machine_Name nvarchar(255) './Fully-Qualified-Machine-Name',
	NP_Policy_Name nvarchar(255) './NP-Policy-Name',
	MS_Quarantine_State int './MS-Quarantine-State',
	MS_Extended_Quarantine_State int './MS-Extended-Quarantine-State',
	System_Health_Result nvarchar(4000),
	System_Health_ResultEx nvarchar(MAX),
	MS_Network_Access_Server_Type int './MS-Network-Access-Server-Type',
	Called_Station_Id nvarchar(255) './Called-Station-Id',
	MS_Quarantine_Grace_Time datetime './MS-Quarantine-Grace-Time',
	MS_Quarantine_User_Class nvarchar(255) './MS-Quarantine-User-Class',
	Client_IPv6_Address nvarchar(32) './Client-IPv6-Address',
	Not_Quarantine_Capable int './Not-Quarantine-Capable',
	AFW_Zone int './AFW-Zone',
	AFW_Protection_Level int './AFW-Protection-Level',
	Quarantine_Update_Non_Compliant int './Quarantine-Update-Non-Compliant',
	MS_Machine_Name nvarchar(255) './MS-Machine-Name',
	OS_Version nvarchar(255) './Machine-Inventory',
	MS_Quarantine_Session_Id nvarchar(255) './MS-Quarantine-Session-Id'
	)

EXEC sp_xml_removedocument @idoc

SET NOCOUNT OFF
GO
/****** Object:  StoredProcedure [dbo].[Request_Licenses]    Script Date: 4/9/2026 10:56:27 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE PROCEDURE [dbo].[Request_Licenses]
   @Company_Name NVARCHAR(MAX),
    @ProductID INT,
    @ClientID INT,
    @GoverID INT,
    @CityID INT,
    @Address NVARCHAR(MAX),
    @Result INT OUTPUT
AS
BEGIN
	
	SET NOCOUNT ON;
	DECLARE @Cost INT;
	DECLARE @InsertedIDs TABLE (ID INT);
	
	SELECT @Cost = License_Cost FROM Products WHERE Products.ID = @ProductID;
	SET NOCOUNT ON;

    INSERT INTO Licenses (Company_Name, ProductID, ClientID, GoverID, CityID, Address, Cost)
    OUTPUT inserted.ID INTO @InsertedIDs
    VALUES (@Company_Name, @ProductID, @ClientID, @GoverID, @CityID, @Address, @Cost);
		SELECT @Result = ID FROM @InsertedIDs;
		
END
GO
/****** Object:  StoredProcedure [dbo].[sp_CheckForUpdate]    Script Date: 4/9/2026 10:56:27 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE PROCEDURE [dbo].[sp_CheckForUpdate]
    @ApplicationName varchar(50),
    @CurrentVersion varchar(50),
		@ComputerID varchar(255),
    @UpdateLink varchar(255) OUTPUT,
    @Filename VARCHAR(50) OUTPUT,
    @LatestVersion VARCHAR(50) OUTPUT,
    @App_Terminate VARCHAR(255) OUTPUT,
    @IsDBUpdate VARCHAR(5) OUTPUT,
    @DB_Link VARCHAR(255) OUTPUT
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @UpdateLinkFound varchar(200);
    DECLARE @DownloadTimes int;
    DECLARE @IsActive int;
		DECLARE @Lic_ID INT;
		
		SELECT @Lic_ID = Licenses.ID FROM Keys	INNER JOIN Licenses ON Keys.License_ID = Licenses.ID WHERE Keys.Computer_ID = @ComputerID;
		UPDATE Licenses SET Application_Version = @CurrentVersion , LastOnline = GETDATE() WHERE ID = @Lic_ID;
		
		SELECT @IsActive = Technical_Support.IsActive FROM Keys	INNER JOIN Technical_Support ON Keys.License_ID = Technical_Support.LicenseID WHERE Keys.Computer_ID = @ComputerID;
		IF(@IsActive = 1)
		BEGIN
			SELECT TOP 1 @LatestVersion = VersionNumber,
									 @UpdateLinkFound = UpdateLink,
									 @Filename = FileName,
									 @App_Terminate = AppTerminate,
									 @IsDBUpdate = IsDBUpdate,
									 @DB_Link = DBLink                 
			FROM ApplicationVersions
			WHERE ApplicationName = @ApplicationName AND VersionNumber > @CurrentVersion AND IsActive = 1
			ORDER BY VersionNumber ASC;

			IF @LatestVersion IS NOT NULL
			BEGIN
					SET @UpdateLink = @UpdateLinkFound;

					-- Increment the Download_Times column by 1
					UPDATE ApplicationVersions
					SET Download_Times = Download_Times + 1
					WHERE ApplicationName = @ApplicationName AND VersionNumber = @LatestVersion;
			END
			ELSE
			BEGIN
					SET @UpdateLink = 'No updates available';
					SET @App_Terminate = NULL;
					SET @IsDBUpdate = NULL;
					SET @DB_Link = NULL;
			END
		END
		ELSE
			BEGIN
				SET @UpdateLink = 'No updates available';
				SET @App_Terminate = NULL;
				SET @IsDBUpdate = NULL;
				SET @DB_Link = NULL;
			END
		
END
GO
USE [master]
GO
ALTER DATABASE [license] SET  READ_WRITE 
GO
