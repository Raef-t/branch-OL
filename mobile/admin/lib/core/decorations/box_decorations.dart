import 'package:flutter/material.dart';
import '/core/border_radius/circulars.dart';
import '/core/border_radius/onlys.dart';
import '/core/helpers/build_box_shadow_blur3_and_spread1_and_offset_y1_go_to_bottom_and_x0_helper.dart';
import '/core/helpers/build_box_shadow_blur6_and_spread2_and_offset_x0_and_y2_go_to_bottom_helper.dart';
import '/core/helpers/build_linear_gradient_circle_in_q_r_bottom_navigation_bar_helper.dart';
import '/core/helpers/build_linear_gradient_to_app_bar_helper.dart';
import '/core/helpers/build_linear_gradient_to_big_circle_in_courses_view_helper.dart';
import '/core/helpers/build_linear_gradient_to_card_in_filter_view_helper.dart';
import '/core/helpers/build_linear_gradient_to_register_card_button_in_auth_view_helper.dart';
import '/core/helpers/build_linear_gradient_to_save_button_in_filter_exams_view2_helepr.dart';
import '/core/helpers/build_linear_gradient_to_selected_card_tab_bar_in_courses_details_view_helper.dart';
import '/core/styles/colors_style.dart';
import '/gen/assets.gen.dart';

abstract class BoxDecorations {
  static BoxDecoration boxDecorationToFilterCardComponent({
    required BuildContext context,
    required bool isRotait,
    required ImageProvider imageProvider,
  }) {
    return BoxDecoration(
      borderRadius: isRotait
          ? Circulars.circular5(context: context)
          : Circulars.circular10(context: context),
      color: ColorsStyle.littleGreyColor,
      image: DecorationImage(image: imageProvider),
    );
  }

  static BoxDecoration boxDecorationToExamNumbersTodayCardHomeView({
    required BuildContext context,
  }) {
    return BoxDecoration(
      color: ColorsStyle.backSection,
      borderRadius: Circulars.circular10(context: context),
    );
  }

  static BoxDecoration
  boxDecorationVerticalLineThatClipperFromTopLeftAndBottomLeftComponent({
    required BuildContext context,
    required Color color,
  }) {
    return BoxDecoration(
      color: color,
      borderRadius: Onlys.topLeft55AndBottomLeft55(context: context),
    );
  }

  static BoxDecoration boxDecorationToCircleInQRBottomNavigationBar({
    required BuildContext context,
  }) {
    return BoxDecoration(
      shape: BoxShape.circle,
      gradient: buildLinearGradientCircleInQRBottomNavigationBarHelper(),
    );
  }

  static BoxDecoration
  boxDecorationToFullDateCardSelectedAndUnSelectedComponent({
    required BuildContext context,
    required Color color,
  }) {
    return BoxDecoration(
      color: color.withAlpha(175),
      borderRadius: Circulars.circular6(context: context),
    );
  }

  static BoxDecoration boxDecorationToCircleDateInsideFullDateCardComponent({
    required BuildContext context,
  }) {
    return BoxDecoration(
      shape: BoxShape.circle,
      gradient: buildLinearGradientCircleInQRBottomNavigationBarHelper(),
    );
  }

  static BoxDecoration boxDecorationToAppBarCard() {
    return BoxDecoration(gradient: buildLinearGradientToAppBarHelper());
  }

  static BoxDecoration boxDecorationToBackgroundBodyView({
    required BuildContext context,
  }) {
    return BoxDecoration(
      color: ColorsStyle.backSection,
      borderRadius: Onlys.topLeft30AndTopRight30(context: context),
    );
  }

  static BoxDecoration boxDecorationExamsCardInExamView({
    required BuildContext context,
  }) {
    return BoxDecoration(
      color: ColorsStyle.whiteColor,
      borderRadius: Circulars.circular10(context: context),
    );
  }

  static BoxDecoration boxDecorationToCardsInMenuTabBarWorkHoursView({
    required Color color,
    required BorderRadius borderRadius,
  }) {
    return BoxDecoration(color: color, borderRadius: borderRadius);
  }

  static BoxDecoration boxDecorationToCheckboxComponent({
    required BuildContext context,
  }) {
    return BoxDecoration(
      borderRadius: Circulars.circular1(context: context),
      color: ColorsStyle.whiteColor,
      border: Border.all(color: ColorsStyle.veryLittleBrownColor),
    );
  }

  static BoxDecoration boxDecorationToCardInFilterView({
    required BuildContext context,
    required bool isNeedToGradient,
  }) {
    return BoxDecoration(
      borderRadius: Circulars.circular10(context: context),
      gradient: isNeedToGradient
          ? buildLinearGradientToCardInFilterViewHelper()
          : null,
      color: isNeedToGradient ? null : ColorsStyle.littleGreyColor,
    );
  }

  static BoxDecoration boxDecorationToScanOrDisplayQRComponent({
    required BuildContext context,
  }) {
    return BoxDecoration(
      color: ColorsStyle.whiteColor,
      borderRadius: Onlys.topLeft20AndTopRight20AndBottomLeft20(
        context: context,
      ),
    );
  }

  static BoxDecoration boxDecorationToCardInApplyTabViewInWorkHoursView({
    required BuildContext context,
    required Color color,
  }) {
    return BoxDecoration(
      color: color,
      borderRadius: Circulars.circular6(context: context),
    );
  }

  static BoxDecoration boxDecorationToBigCircleInCoursesView() {
    return BoxDecoration(
      shape: BoxShape.circle,
      gradient: buildLinearGradientToBigCircleInCoursesViewHelper(),
    );
  }

  static BoxDecoration boxDecorationToCardInCoursesView({
    required BuildContext context,
    required Color color,
  }) {
    return BoxDecoration(
      color: color.withAlpha(150),
      borderRadius: Onlys.topLeft10AndTopRight10AndBottomRight(
        context: context,
      ),
    );
  }

  static BoxDecoration boxDecorationToStudentCircleInCoursesView() {
    return BoxDecoration(
      color: ColorsStyle.mediumWhiteColor,
      shape: BoxShape.circle,
      image: DecorationImage(image: Assets.images.greenStudentImage.provider()),
    );
  }

  static BoxDecoration boxDecorationToTeacherImageInCoursesView() {
    return BoxDecoration(
      shape: BoxShape.circle,
      image: DecorationImage(image: Assets.images.profileMissImage.provider()),
    );
  }

  static BoxDecoration boxDecorationToWorldImageInCoursesView() {
    return BoxDecoration(
      shape: BoxShape.circle,
      color: ColorsStyle.mediumWhiteColor,
      image: DecorationImage(image: Assets.images.pinkWorldImage.provider()),
    );
  }

  static BoxDecoration
  boxDecorationToCircleThatContainOnRightArrowIndicateToTopRightImage({
    required BuildContext context,
  }) {
    return BoxDecoration(
      color: ColorsStyle.mediumWhiteColor2,
      boxShadow: [
        buildBoxShadowBlur3AndSpread1AndOffsetY1GoToBottomAndX0Helper(
          context: context,
        ),
      ],
      shape: BoxShape.circle,
      image: DecorationImage(
        image: Assets.images.rightArrowIndicateToTopRightImage.provider(),
      ),
    );
  }

  static BoxDecoration boxDecorationToCardTabBarInCoursesDetailsView({
    required BuildContext context,
  }) {
    return BoxDecoration(
      color: ColorsStyle.littleGreyColor,
      borderRadius: Circulars.circular10(context: context),
      boxShadow: [
        buildBoxShadowBlur3AndSpread1AndOffsetY1GoToBottomAndX0Helper(
          context: context,
        ),
      ],
    );
  }

  static BoxDecoration boxDecorationToDetailsCardInCoursesDetailsView({
    required BuildContext context,
  }) {
    return BoxDecoration(
      color: ColorsStyle.whiteColor,
      borderRadius: Circulars.circular10(context: context),
    );
  }

  static BoxDecoration boxDecorationToDotInsideCardTabBarInCoursesViewDetails({
    required Color color,
  }) {
    return BoxDecoration(color: color, shape: BoxShape.circle);
  }

  static BoxDecoration
  boxDecorationToTeacherImageInsideDetailsCardInCoursesDetailsView() {
    return const BoxDecoration(shape: BoxShape.circle);
  }

  static BoxDecoration
  boxDecorationToSelectedCardToCardTabBarInCoursesDetailsView({
    required BuildContext context,
  }) {
    return BoxDecoration(
      borderRadius: Circulars.circular10(context: context),
      gradient:
          buildLinearGradientToSelectedCardTabBarInCoursesDetailsViewHelper(),
    );
  }

  static BoxDecoration boxDecorationToCheckboxSelectAllInClassView({
    required bool isChecked,
  }) {
    return BoxDecoration(
      color: isChecked ? ColorsStyle.littleVinicColor : null,
      border: Border.all(color: ColorsStyle.blackColor),
      image: isChecked
          ? DecorationImage(image: Assets.images.bigCheckImage.provider())
          : null,
    );
  }

  static BoxDecoration
  boxDecorationToMessageCardInBottomNavigationBarInClassView({
    required BuildContext context,
  }) {
    return BoxDecoration(
      color: ColorsStyle.whiteColor,
      borderRadius: Onlys.topLeft24AndTopRight24AndBottomRight24(
        context: context,
      ),
    );
  }

  static BoxDecoration
  boxDecorationToTakePresenceCardInBottomNavigationBarInClassView({
    required BuildContext context,
  }) {
    return BoxDecoration(
      color: ColorsStyle.whiteColor,
      borderRadius: Onlys.topLeft24AndTopRight24AndBottomLeft24(
        context: context,
      ),
    );
  }

  static BoxDecoration boxDecorationToNotificationCardInDetailsStudentView({
    required BuildContext context,
  }) {
    return BoxDecoration(
      color: ColorsStyle.mediumWhiteColor,
      borderRadius: Circulars.circular5(context: context),
      image: DecorationImage(image: Assets.images.notificationImage.provider()),
    );
  }

  static BoxDecoration boxDecorationToCardContainOnImageComponent({
    required BuildContext context,
    required ImageProvider imageProvider,
  }) {
    return BoxDecoration(
      borderRadius: Circulars.circular5(context: context),
      color: ColorsStyle.mediumWhiteColor,
      image: DecorationImage(image: imageProvider),
      boxShadow: [
        buildBoxShadowBlur3AndSpread1AndOffsetY1GoToBottomAndX0Helper(
          context: context,
        ),
      ],
    );
  }

  static BoxDecoration boxDecorationToMediumCircleDotComponent({
    required Color color,
  }) {
    return BoxDecoration(color: color, shape: BoxShape.circle);
  }

  static BoxDecoration boxDecorationToRatingCardInDetailsStudentView({
    required BuildContext context,
  }) {
    return BoxDecoration(
      color: ColorsStyle.whiteColor,
      borderRadius: Circulars.circular5(context: context),
    );
  }

  static BoxDecoration boxDecorationToNotificationCardInNotificationsView({
    required bool isReaded,
  }) {
    return BoxDecoration(
      color: isReaded
          ? ColorsStyle.transparentColor
          : ColorsStyle.veryLittlePinkColor3,
      borderRadius: BorderRadius.zero,
    );
  }

  static BoxDecoration boxDecorationToVerticalMenuCardInNotificationsView({
    required BuildContext context,
  }) {
    return BoxDecoration(
      color: ColorsStyle.whiteColor,
      borderRadius: Circulars.circular5(context: context),
      boxShadow: [
        buildBoxShadowBlur6AndSpread2AndOffsetX0AndY2GoToBottomHelper(
          context: context,
        ),
      ],
    );
  }

  static BoxDecoration boxDecorationToRestAndTotalPriceCardInPaymentsView({
    required BuildContext context,
    required Color color,
  }) {
    return BoxDecoration(
      color: color,
      borderRadius: Circulars.circular10(context: context),
    );
  }

  static BoxDecoration boxDecorationToSubjectCardInFilterExamsView2({
    required BuildContext context,
    required int index,
    required int selectedSubjectCard,
  }) {
    return BoxDecoration(
      color: selectedSubjectCard == index
          ? ColorsStyle.veryLittlePinkColor4
          : ColorsStyle.littleGreyColor,
      borderRadius: Circulars.circular5(context: context),
      border: Border.all(
        color: selectedSubjectCard == index
            ? ColorsStyle.deepPinkColor2
            : ColorsStyle.transparentColor,
      ),
    );
  }

  static BoxDecoration boxDecorationToMarkExamCardInFilterExamsView2({
    required BuildContext context,
  }) {
    return BoxDecoration(
      borderRadius: Circulars.circular5(context: context),
      color: ColorsStyle.greyColor.withAlpha(50),
    );
  }

  static BoxDecoration boxDecorationToSaveButtonCardInFilterExamsView2({
    required BuildContext context,
  }) {
    return BoxDecoration(
      borderRadius: Circulars.circular5(context: context),
      gradient: buildLinearGradientToSaveButtonInFilterExamsView2Helper(),
    );
  }

  static BoxDecoration boxDecorationToNextCardInSplashView({
    required BuildContext context,
  }) {
    return BoxDecoration(
      color: ColorsStyle.whiteColor.withAlpha(2),
      borderRadius: Circulars.circular72(context: context),
      border: Border.all(
        color: ColorsStyle.whiteColor.withAlpha(130),
        width: 1.5,
      ),
    );
  }

  static BoxDecoration boxDecorationToRegisterCardButtonInAuthView({
    required BuildContext context,
  }) {
    return BoxDecoration(
      borderRadius: Circulars.circular10(context: context),
      gradient: buildLinearGradientToRegisterCardButtonInAuthViewHelper(),
    );
  }

  static BoxDecoration boxDecorationToBottomNavigationBarToManyViews({
    required BuildContext context,
  }) {
    return BoxDecoration(
      border: const Border(
        bottom: BorderSide(color: ColorsStyle.mediumRussetColor),
      ),
      borderRadius: Circulars.circular72(context: context),
      color: ColorsStyle.whiteColor.withAlpha(2),
    );
  }

  static BoxDecoration boxDecorationToCardInExamsToStudentView({
    required BuildContext context,
  }) {
    return BoxDecoration(
      color: ColorsStyle.whiteColor,
      borderRadius: Circulars.circular10(context: context),
    );
  }

  static BoxDecoration boxDecorationToBlackBackgroundInSplashView() {
    return BoxDecoration(
      gradient: LinearGradient(
        begin: Alignment.topCenter,
        end: Alignment.bottomCenter,
        colors: [
          ColorsStyle.mediumBlackColor.withAlpha(2),
          ColorsStyle.mediumBlackColor2,
        ],
      ),
    );
  }

  static BoxDecoration boxDecorationToAttendaceCardInAttendaceView({
    required BuildContext context,
  }) {
    return BoxDecoration(
      color: ColorsStyle.whiteColor,
      borderRadius: Circulars.circular10(context: context),
    );
  }
}
