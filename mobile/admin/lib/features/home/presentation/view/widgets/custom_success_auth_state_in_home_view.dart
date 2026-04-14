import 'package:flutter/material.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/core/sized_boxs/widths.dart';
import '/features/auth/presentation/managers/models/user_model.dart';
import '/features/home/presentation/view/widgets/custom_profile_miss_image_home_view.dart';
import '/features/home/presentation/view/widgets/custom_two_texts_details_to_teacher_and_hand_image_home_view.dart';

class CustomSuccessAuthStateInHomeView extends StatelessWidget {
  const CustomSuccessAuthStateInHomeView({
    super.key,
    required this.userModel,
    required this.userName,
    required this.userPhoto,
  });
  final UserModel? userModel;
  final String userName, userPhoto;
  @override
  Widget build(BuildContext context) {
    return OnlyPaddingWithChild.right20(
      context: context,
      child: Row(
        children: [
          CustomTwoTextsDetailsToTeacherAndHandImageHomeView(
            userModel: userModel,
            userName: userName,
          ),
          Widths.width8(context: context),
          CustomProfileMissImageHomeView(
            userModel: userModel,
            userPhoto: userPhoto,
          ),
        ],
      ),
    );
  }
}
