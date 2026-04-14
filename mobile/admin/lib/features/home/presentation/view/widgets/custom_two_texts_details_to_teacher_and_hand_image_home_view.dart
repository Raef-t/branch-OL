import 'package:flutter/material.dart';
import '/core/sized_boxs/heights.dart';
import '/core/styles/colors_style.dart';
import '/core/styles/texts_style.dart';
import '/features/auth/presentation/managers/models/user_model.dart';
import '/features/home/presentation/view/widgets/custom_image_and_text_in_app_bar_home_view.dart';
import '/gen/fonts.gen.dart';

class CustomTwoTextsDetailsToTeacherAndHandImageHomeView
    extends StatelessWidget {
  const CustomTwoTextsDetailsToTeacherAndHandImageHomeView({
    super.key,
    required this.userModel,
    required this.userName,
  });
  final UserModel? userModel;
  final String userName;
  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.end,
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        CustomImageAndTextInAppBarHomeView(
          userModel: userModel,
          userName: userName,
        ),
        Heights.height2(context: context),
        Text(
          'مشرف في أكادمية العلماء',
          style: TextsStyle.normal14(context: context).copyWith(
            fontFamily: FontFamily.inter,
            color: ColorsStyle.veryLittleBlackColor,
          ),
        ),
      ],
    );
  }
}
